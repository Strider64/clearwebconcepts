function loadCollectJS() {
    const script = document.createElement('script');
    script.src = "https://secure.networkmerchants.com/token/Collect.js";
    script.setAttribute('data-tokenization-key', 'bDjTCZ-uw222J-8jPpA3-X5s4bW');
    script.setAttribute('data-variant', 'inline');
    script.setAttribute('data-field-ccnumber-placeholder', '0000 0000 0000 0000');
    script.setAttribute('data-field-ccexp-placeholder', '10/25');
    script.setAttribute('data-field-cvv-placeholder', '123');
    script.setAttribute('data-custom-css', JSON.stringify({
        "background-color": "#a0a0ff",
        "color": "#0000ff",
        "padding": "6px",
        "font-size": "16px",
        "height": "33px"
    }));

    script.onload = function() {
        console.log('Collect.js loaded successfully');
    };

    script.onerror = function() {
        console.error('Failed to load Collect.js');
    };

    document.body.appendChild(script);
}

document.addEventListener('DOMContentLoaded', function() {
    loadCollectJS();
});
