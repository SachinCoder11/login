<?php
// ✅ Display a message when the page loads
echo "Signup page loaded!";

// ✅ Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ reCAPTCHA Validation
    $recaptchaSecret = 'YOUR_SECRET_KEY_HERE';  // Replace with your secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        die("❌ reCAPTCHA failed. Please try again.");
    }

    // ✅ Establish Database Connection
    $conn = new mysqli("localhost", "root", "", "travel_login_data");

    if ($conn->connect_error) {
        die("❌ Database connection failed: " . $conn->connect_error);
    }

    // ✅ Sanitize User Input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ✅ Validate Email Format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format!");
    }

    // ✅ Hash Password Before Storing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert Data into Database
    $sql = "INSERT INTO `login_data` (`Username`, `Email`, `Contact`, `Password`) 
            VALUES ('$username', '$email', '$contact', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        // ✅ Redirect to landing page after successful signup
        header("Location: landing-page.html");
        exit();
    } else {
        echo "❌ Error: " . $conn->error;
    }

    // ✅ Close Database Connection
    $conn->close();
}
?>