import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Inside the relevant function or event handler
Swal.fire({
    title: 'Success',
    text: response.data.message, // Assuming the response contains the notification message
    icon: 'success',
    timer: 3000, // Duration to display the notification (in milliseconds)
});
