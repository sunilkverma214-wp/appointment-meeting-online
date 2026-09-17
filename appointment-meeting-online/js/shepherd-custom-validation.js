jQuery(document).ready(function($) {
    // Define the ID of your custom fields
    const requiredFields = [
        '#first_name_field',
        '#last_name_field',
        '#sub_title_field',
        '#contact_email_field',
        '#default_working_hours_start',
        '#default_working_hours_end',     
                      
        // Add more field IDs as needed
    ];

    $('#publish').click(function(e) {
        let allFieldsFilled = true;

        requiredFields.forEach(function(field) {
            if ($(field).length && !$(field).val()) {
                allFieldsFilled = false;
                $(field).css('border', '1px solid red'); // Highlight the empty field
            } else {
                $(field).css('border', ''); // Reset the border if filled
            }
        });
        

         // Validate that at least one checkbox is checked for working days
         let isCheckboxChecked = $('input[name="default_working_days[]"]:checked').length > 0;
         if (!isCheckboxChecked) {
            allFieldsFilled = false;
             //alert('Please select at least one working day.');            
             $('.weekday-checkbox').css('border', '1px solid red'); // Highlight the empty field
         }
        if (!allFieldsFilled) {
            e.preventDefault();
             // Scroll to the shepherd_date div if it is part of the error
             $('html, body').animate({
                scrollTop: $('#shepherd_date').offset().top - 100 // Adjust -100 for padding above the element
            }, 500);

            //firstEmptyField.focus(); // Focus on the first empty field or checkbox
            //alert('Please fill in all required fields.');
        }
    });
});