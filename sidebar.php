<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-3 col-lg-2 px-0">
    <div class="bg-dark text-white min-vh-100 p-3 d-flex flex-column">

        <div class="text-center mb-4">
            <h4 class="fw-bold">SHMS</h4>
            <small class="text-light">Smart Healthcare</small>
        </div>

        <hr class="border-light">

        <ul class="nav flex-column">

            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link text-white <?php echo ($current_page == 'dashboard.php') ? 'bg-primary rounded' : ''; ?>">
                    🏠 Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="view.php" class="nav-link text-white <?php echo ($current_page == 'view.php' || $current_page == 'form.php' || $current_page == 'update.php') ? 'bg-primary rounded' : ''; ?>">
                    👤 Patients
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="doctor_view.php" class="nav-link text-white <?php echo ($current_page == 'doctor_view.php' || $current_page == 'doctor_form.php' || $current_page == 'doctor_update.php') ? 'bg-primary rounded' : ''; ?>">
                    🩺 Doctors
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="appointment_view.php" class="nav-link text-white <?php echo ($current_page == 'appointment_view.php' || $current_page == 'appointment_form.php' || $current_page == 'appointment_update.php') ? 'bg-primary rounded' : ''; ?>">
                    📅 Appointments
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="prescription_view.php" class="nav-link text-white <?php echo ($current_page == 'prescription_view.php' || $current_page == 'prescription_form.php' || $current_page == 'prescription_update.php') ? 'bg-primary rounded' : ''; ?>">
                    💊 Prescriptions
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="billing_view.php" class="nav-link text-white <?php echo ($current_page == 'billing_view.php' || $current_page == 'billing_form.php' || $current_page == 'billing_update.php') ? 'bg-primary rounded' : ''; ?>">
                    💳 Billing
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="insurance_view.php" class="nav-link text-white <?php echo ($current_page == 'insurance_view.php' || $current_page == 'insurance_form.php' || $current_page == 'insurance_update.php') ? 'bg-primary rounded' : ''; ?>">
                    🛡️ Insurance
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="investigation_view.php" class="nav-link text-white <?php echo ($current_page == 'investigation_view.php' || $current_page == 'investigation_form.php' || $current_page == 'investigation_update.php') ? 'bg-primary rounded' : ''; ?>">
                    🧾 Investigation
                </a>
            </li>

        </ul>

        <div class="mt-auto text-center small text-light pt-3">
            © 2026 SHMS
        </div>

    </div>
</div>