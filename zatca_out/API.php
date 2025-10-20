<?php

namespace ZATCA;

use Exception;

class API
{
    private $base_url_production = 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal';
    private $base_url_sandbox = 'https://gw-apic-gov.gazt.gov.sa/e-invoicing/developer-portal';
    private $certificate;
    private $secret;
    private $production = false;

    public function __construct(bool $production = false)
    {
        $this->production = $production;
    }

    private function getBaseUrl(): string
    {
        return $this->production ? $this->base_url_production : $this->base_url_sandbox;
    }

    public function compliance(?string $certificate = null, ?string $secret = null): array
    {
        if ($certificate) {
            $this->certificate = $certificate;
        }
        if ($secret) {
            $this->secret = $secret;
        }

        $issueCertificate = function (string $csr, string $otp) {
            return $this->issueCertificate($csr, $otp);
        };

        $checkInvoiceCompliance = function (string $invoice_xml, string $invoice_hash, string $uuid) {
            return $this->checkInvoiceCompliance($invoice_xml, $invoice_hash, $uuid);
        };

        return [$issueCertificate, $checkInvoiceCompliance];
    }

    private function issueCertificate(string $csr, string $otp): object
    {
        $url = $this->getBaseUrl() . '/compliance';

        $data = [
            'csr' => $csr
        ];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Accept-Version: V2',
            'OTP: ' . $otp
        ];

        $response = $this->makeRequest($url, 'POST', $data, $headers);

        if (!$response || !isset($response->binarySecurityToken)) {
            throw new Exception('Failed to issue certificate: ' . json_encode($response));
        }

        return $response;
    }

    private function checkInvoiceCompliance(string $invoice_xml, string $invoice_hash, string $uuid): object
    {
        $url = $this->getBaseUrl() . '/compliance/invoices';

        $data = [
            'invoiceHash' => $invoice_hash,
            'uuid' => $uuid,
            'invoice' => base64_encode($invoice_xml)
        ];

        $auth = base64_encode($this->certificate . ':' . $this->secret);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Accept-Version: V2',
            'Authorization: Basic ' . $auth
        ];

        $response = $this->makeRequest($url, 'POST', $data, $headers);

        if (!$response) {
            throw new Exception('Failed to check invoice compliance');
        }

        return $response;
    }

    private function makeRequest(string $url, string $method, array $data, array $headers): ?object
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // For development/testing - you may want to disable SSL verification
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('CURL Error: ' . $error);
        }

        curl_close($ch);

        if ($http_code >= 400) {
            throw new Exception('HTTP Error ' . $http_code . ': ' . $response);
        }

        return json_decode($response);
    }
}
