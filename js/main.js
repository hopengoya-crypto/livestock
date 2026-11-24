function checkPhoneNumber() {
    const phone = document.querySelector('#phone');
    const message = document.querySelector('#phone-message');

    const phonePattern = /^(07|01)\d{8}$/;

    if (phonePattern.test(phone.value.trim())) {
        message.textContent = ' ';
        phone.classList.remove('invalid');
    } 
else {
        message.textContent = 'Enter a valid Kenyan phone number.';
        phone.classList.add('invalid');
    }
}
function checkEmail() {
    const email = document.querySelector('#email');
    const message = document.querySelector('#email-message');

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailPattern.test(email.value.trim())) {
        message.textContent = ' ';
        email.classList.remove('invalid');
    }  
else {
        message.textContent = 'Enter a valid email address.';
        email.classList.add('invalid');
    }
}
function checkPassword() {
    const password = document.querySelector('#password');
    const message = document.querySelector('#password-message');

    const passwordPattern =
        /^(?=.[a-z])(?=.[A-Z])(?=.*\d).{8,}$/;

    if (passwordPattern.test(password.value)) {
        message.textContent = '';
        password.classList.remove('invalid');
    } else {
        message.textContent =
            'Password must have at least 8 characters, an uppercase letter, a lowercase letter and a number.';

        password.classList.add('invalid');
    }
}
function confirmPassword() {
    const password = document.querySelector('#password');
    const confirm = document.querySelector('#confirm-password');
    const message = document.querySelector('#confirm-message');

    if (password.value === confirm.value && confirm.value !== '') {
        message.textContent = '';
        confirm.classList.remove('invalid');
    } else {
        message.textContent = 'Passwords do not match.';
        confirm.classList.add('invalid');
    }
}
function checkScheduleDate() {
    const scheduleDate = document.querySelector('#schedule-date');
    const message = document.querySelector('#schedule-message');

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const selectedDate = new Date(scheduleDate.value);

    if (scheduleDate.value && selectedDate >= today) {
        message.textContent = '';
        scheduleDate.classList.remove('invalid');
    } else {
        message.textContent =
            'Please select today or a future date.';

        scheduleDate.classList.add('invalid');
    }
}
function checkLivestockDetails() {
    const name = document.querySelector('#livestock-name');
    const species = document.querySelector('#species');
    const breed = document.querySelector('#breed');
    const gender = document.querySelector('#gender');

    if (
        name.value.trim() !== '' &&
        species.value !== '' &&
        breed.value.trim() !== '' &&
        gender.value !== ''
    ) {
        alert('Livestock details are complete.');
        return true;
    } else {
        alert('Please complete all livestock details.');
        return false;
    }
}
function calculateReminderDate() {
    const scheduleDate =
        document.querySelector('#schedule-date').value;

    const reminderDate =
        document.querySelector('#reminder-date');

    if (!scheduleDate) {
        alert('Please select a vaccination date first.');
        return;
    }

    const date = new Date(scheduleDate + 'T00:00:00');

    date.setDate(date.getDate() - 14);

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    reminderDate.value = `${year}-${month}-${day}`;
}
function checkVaccinationStatus() {
    const dateInput = document.querySelector('#schedule-date');
    const status = document.querySelector('#vaccination-status');

    if (!dateInput.value) {
        status.textContent = 'No schedule date selected.';
        return;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const vaccinationDate =
        new Date(dateInput.value + 'T00:00:00');

    if (status.dataset.completed === 'true') {
        status.textContent = 'Completed';
    } else if (vaccinationDate < today) {
        status.textContent = 'Overdue';
    } else {
        status.textContent = 'Pending';
    }
}
function togglePassword() {
    const password = document.querySelector('#password');

    if (password.type === 'password') {
        password.type = 'text';
    } else {
        password.type = 'password';
    }
}
function confirmDelete() {
    const answer = confirm(
        'Are you sure you want to delete this record?'
    );

    if (answer) {
        alert('Deletion confirmed.');
        return true;
    } else {
        alert('Deletion cancelled.');
        return false;
    }
}