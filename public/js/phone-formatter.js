/**
 * Bangladesh Phone Number Auto-Formatter
 * Automatically adds +880 country code to phone numbers
 * 
 * Usage: Include this script and add class "bd-phone" to phone input fields
 */

function formatBangladeshPhone(input) {
    let value = input.value.replace(/\D/g, ''); // Remove all non-digits
    
    // If already starts with 880, extract the rest
    if (value.startsWith('880')) {
        value = value.substring(3);
    }
    
    // If starts with 0, remove it
    if (value.startsWith('0')) {
        value = value.substring(1);
    }
    
    // Limit to 10 digits (after +880)
    value = value.substring(0, 10);
    
    // Add +880 prefix if there's any number
    if (value.length > 0) {
        input.value = '+880' + value;
    } else {
        input.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Find all phone inputs (by name containing 'phone' or by class 'bd-phone')
    const phoneInputs = document.querySelectorAll(
        'input[name*="phone"], input.bd-phone, input[type="tel"]'
    );
    
    phoneInputs.forEach(function(input) {
        // Format as user types
        input.addEventListener('input', function() {
            formatBangladeshPhone(this);
        });
        
        // Format when user leaves field
        input.addEventListener('blur', function() {
            formatBangladeshPhone(this);
        });
        
        // Format existing value on load
        if (input.value && input.value.length > 0) {
            formatBangladeshPhone(input);
        }
    });
});
