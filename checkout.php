<html>
<head>
    <script src="https://secure.networkmerchants.com/token/Collect.js" data-tokenization-key="53tySU-VjKY83-qKzgAg-pnzH5S"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, shrink-to-fit=no">
    <style>
        input {
            border: 5px inset #687C8D;
            background-color: #c0c0c0;
            color: green;
            font-size: 25px;
            font-family: monospace;
            padding: 5px;
        }
    </style>
</head>
<body>
<h1>Inline CollectJS Demo</h1>
<form action="/your-page.php" method="post">
    <table>
        <tr>
            <td>Amount: </td>
            <td><input size="50" type="text" name="amount" value="1.00" /></td>
        </tr>
        <tr>
            <td>First Name: </td>
            <td><input size="50" type="text" name="first_name" value="Test" /></td>
        </tr>
        <tr>
            <td>Last Name: </td>
            <td><input size="50" type="text" name="last_name" value="User" /></td>
        </tr>
        <tr>
            <td>Address1</td>
            <td><input size="50" type="text" name="address1" value="123 Main Street"></td>
        </tr>
        <tr>
            <td>City</td>
            <td><input size="50" type="text" name="city" value="Beverley Hills"></td>
        </tr>
        <tr>
            <td>State</td>
            <td><input size="50" type="text" name="state" value="CA"></td>
        </tr>
        <tr>
            <td>zip</td>
            <td><input size="50" type="text" name="zip" value="90210"></td>
        </tr>
        <tr>
            <td>country</td>
            <td><input size="50" type="text" name="country" value="US"></td>
        </tr>
        <tr>
            <td>phone</td>
            <td><input size="50" type="text" name="phone" value="5555555555"></td>
        </tr>
        <tr>
            <td>CC Number</td>
            <td id="demoCcnumber"></td>
        </tr>
        <tr>
            <td>CC Exp</td>
            <td id="demoCcexp"></td>
        </tr>
        <tr>
            <td>CVV</td>
            <td id="demoCvv"></td>
        </tr>
        <tr>
            <td>Account Number</td>
            <td id="demoCheckaccount"></td>
        </tr>
        <tr>
            <td>Routing Number</td>
            <td id="demoCheckaba"></td>
        </tr>
        <tr>
            <td>Name on Account</td>
            <td id="demoCheckname"></td>
        </tr>
        <tr>
            <td></td>
            <td class="googlePayButton"></td>
        </tr>
        <tr>
            <td></td>
            <td class="applePayButton"></td>
        </tr>
    </table>
    <br>
    <button id="demoPayButton" type="button">Pay the money.</button>
</form>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        CollectJS.configure({
            "paymentSelector" : "#demoPayButton",
            "variant" : "inline",
            "styleSniffer" : "false",
            "googleFont": "Montserrat:400",
            "customCss" : {
                "color": "#0000ff",
                "background-color": "#d0d0ff"
            },
            "invalidCss": {
                "color": "white",
                "background-color": "red"
            },
            "validCss": {
                "color": "black",
                "background-color": "#d0ffd0"
            },
            "placeholderCss": {
                "color": "green",
                "background-color": "#687C8D"
            },
            "focusCss": {
                "color": "yellow",
                "background-color": "#202020"
            },
            "fields": {
                "ccnumber": {
                    "selector": "#demoCcnumber",
                    "title": "Card Number",
                    "placeholder": "0000 0000 0000 0000"
                },
                "ccexp": {
                    "selector": "#demoCcexp",
                    "title": "Card Expiration",
                    "placeholder": "00 / 00"
                },
                "cvv": {
                    "display": "show",
                    "selector": "#demoCvv",
                    "title": "CVV Code",
                    "placeholder": "***"
                },
                "checkaccount": {
                    "selector": "#demoCheckaccount",
                    "title": "Account Number",
                    "placeholder": "0000000000"
                },
                "checkaba": {
                    "selector": "#demoCheckaba",
                    "title": "Routing Number",
                    "placeholder": "000000000"
                },
                "checkname": {
                    "selector": "#demoCheckname",
                    "title": "Name on Checking Account",
                    "placeholder": "Customer McCustomerface"
                },
                "googlePay": {
                    "selector": ".googlePayButton",
                    "shippingAddressRequired": true,
                    "shippingAddressParameters": {
                        "phoneNumberRequired": true,
                        "allowedCountryCodes": ['US', 'CA']
                    },
                    "billingAddressRequired": true,
                    "billingAddressParameters": {
                        "phoneNumberRequired": true,
                        "format": "MIN"
                    },
                    'emailRequired': true,
                    "buttonType": "buy",
                    "buttonColor": "white",
                    "buttonLocale": "en"
                },
                'applePay' : {
                    'selector' : '.applePayButton',
                    'shippingMethods': [
                        {
                            'label': 'Free Standard Shipping',
                            'amount': '0.00',
                            'detail': 'Arrives in 5-7 days',
                            'identifier': 'standardShipping'
                        },
                        {
                            'label': 'Express Shipping',
                            'amount': '10.00',
                            'detail': 'Arrives in 2-3 days',
                            'identifier': 'expressShipping'
                        }
                    ],
                    'shippingType': 'delivery',
                    'requiredBillingContactFields': [
                        'postalAddress',
                        'name'
                    ],
                    'requiredShippingContactFields': [
                        'postalAddress',
                        'name'
                    ],
                    'contactFields': [
                        'phone',
                        'email'
                    ],
                    'contactFieldsMappedTo': 'shipping',
                    'lineItems': [
                        {
                            'label': 'Foobar',
                            'amount': '3.00'
                        },
                        {
                            'label': 'Arbitrary Line Item #2',
                            'amount': '1.00'
                        }
                    ],
                    'totalLabel': 'foobar',
                    'totalType': 'pending',
                    'type': 'buy',
                    'style': {
                        'button-style': 'white-outline',
                        'height': '50px',
                        'border-radius': '0'
                    }
                }
            },
            'price': '1.00',
            'currency':'USD',
            'country': 'US',
            'validationCallback' : function(field, status, message) {
                if (status) {
                    var message = field + " is now OK: " + message;
                } else {
                    var message = field + " is now Invalid: " + message;
                }
                console.log(message);
            },
            "timeoutDuration" : 10000,
            "timeoutCallback" : function () {
                console.log("The tokenization didn't respond in the expected timeframe.  This could be due to an invalid or incomplete field or poor connectivity");
            },
            "fieldsAvailableCallback" : function () {
                console.log("Collect.js loaded the fields onto the form");
            },
            'callback' : function(response) {
                alert(response.token);
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "payment_token";
                input.value = response.token;
                var form = document.getElementsByTagName("form")[0];
                form.appendChild(input);
                form.submit();
            }
        });
    });
</script>
</body>
</html>