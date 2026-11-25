<?php
$page_title = 'Livestock Vaccination Reminder System';
include '../includes/header.php';
?>
<div class="container">
    <div class="form-box" id="select-veterinarian-form">
        <form action="">
            <h3>Select Veterinarian</h3>
            <legend>Request a veterinarian</legend>
            <label for="veterinarian">Veterinarian</label>
            <select id="veterinarian" name="veterinarian" required>
                <option value="">Choose a veterinarian</option>
                <option value="dr_smith">Dr. Smith</option>
                <option value="dr_williams">Dr. Williams</option>
                <option value="dr_brown">Dr. Brown</option>
            </select>
            <button type="submit" name="register">send request</button>
            <p> Once you submit your request, a veterinarian will contact you or send you an email.</p>
            <p>Have your own veterinarian? invite them to join the system.<a href="invite-veterinarian.html"> Invite Veterinarian</a></p>
        </form>
    </div>
</div>
<footer>
<p>Livestock Vaccination Reminder System, PO Box 4021, Kajiado.</p>
<p>Site built by Ngoya Hope & Jessica Muiruri, DBT Year 1, 2026.</p>
</footer>
</body>
</html>
<?php include '../includes/footer.php'; ?>