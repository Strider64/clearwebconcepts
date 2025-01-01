<?php
require 'vendor/autoload.php';

$gatewayURL = 'https://secure.networkmerchants.com/api/v2/three-step'; // Merchant One endpoint
$APIKey = 'g4RRyqNU3wN2VNDk86p58SU428by8nFq'; // Replace with Merchant One API key

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if ($inputData['action'] === 'step_one') {
        // Handle Step One: Collect Non-Sensitive Information
        $billingData = $inputData['billingData'];

        $xmlRequest = new DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlSale = $xmlRequest->createElement('add-customer');

        appendXmlNode($xmlRequest, $xmlSale, 'api-key', $APIKey);
        appendXmlNode($xmlRequest, $xmlSale, 'redirect-url', 'https://www.clearwebconcepts.com/payment.php'); // URL that handles Step Three

        // Add billing information to the XML request
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-first-name', $billingData['first_name']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-last-name', $billingData['last_name']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-address1', $billingData['address']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-city', $billingData['city']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-state', $billingData['state']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-postal', $billingData['zip']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-country', $billingData['country']);
        appendXmlNode($xmlRequest, $xmlSale, 'billing-address-email', $billingData['email']);

        $xmlRequest->appendChild($xmlSale);

        // Send XML to gateway
        $response = sendXMLviaCurl($xmlRequest, $gatewayURL);
        $gwResponse = new SimpleXMLElement($response);

        if ((string)$gwResponse->result == 1) {
            $formURL = (string)$gwResponse->{'form-url'};
            echo json_encode(['form_url' => $formURL]);
        } else {
            echo json_encode(['error' => $gwResponse->{'result-text'}]);
        }
    } elseif ($inputData['action'] === 'step_three') {
        // Handle Step Three: Complete Transaction (Token handling)
        $tokenId = $inputData['token_id'];

        $xmlRequest = new DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
        appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'api-key', $APIKey);
        appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'token-id', $tokenId);
        $xmlRequest->appendChild($xmlCompleteTransaction);

        $data = sendXMLviaCurl($xmlRequest, $gatewayURL);
        $gwResponse = new SimpleXMLElement($data);

        if ((string)$gwResponse->result == 1) {
            echo json_encode(['success' => true, 'transaction_id' => $gwResponse->{'transaction-id'}]);
        } else {
            echo json_encode(['error' => $gwResponse->{'result-text'}]);
        }
    }
}

function sendXMLviaCurl($xmlRequest, $gatewayURL) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gatewayURL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-type: text/xml"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest->saveXML());
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function appendXmlNode($domDocument, $parentNode, $name, $value) {
    $childNode = $domDocument->createElement($name);
    $childNodeValue = $domDocument->createTextNode($value);
    $childNode->appendChild($childNodeValue);
    $parentNode->appendChild($childNode);
}

