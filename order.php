<!--  --><?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = mysqli_real_escape_string($connection, $_POST['fullname']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $itemType = mysqli_real_escape_string($connection, $_POST['itemType']);
    $numItems = mysqli_real_escape_string($connection, $_POST['numItems']);
    $service = mysqli_real_escape_string($connection, $_POST['service']);
    $pickupDate = mysqli_real_escape_string($connection, $_POST['pickupDate']);
    $deliveryDate = mysqli_real_escape_string($connection, $_POST['deliveryDate']);
    $deliveryAddress = mysqli_real_escape_string($connection, $_POST['deliveryAddress']);
    $paymentMethod = mysqli_real_escape_string($connection, $_POST['paymentMethod']);

    // Calculate total price
    $itemPrices = [
        'shirts' => 40,
        'trousers' => 60,
        'jackets' => 40,
        'dresses' => 80,
        'saree' => 80,
        'special_occasion' => 100,
        'curtains' => 100,
        'blankets' => 150,
        'upholstery' => 80
    ];

    $pricePerItem = $itemPrices[$itemType] ?? 0;
    $totalAmount = $pricePerItem * $numItems;

    // Insert into orders table
    $query = "INSERT INTO orders (
        fullname, email, phone, address, 
        itemType, numItems, service, 
        pickupDate, deliveryDate, deliveryAddress, 
        paymentMethod, total_amount, order_status
    ) VALUES (
        '$fullname', '$email', '$phone', '$address',
        '$itemType', '$numItems', '$service',
        '$pickupDate', '$deliveryDate', '$deliveryAddress',
        '$paymentMethod', $totalAmount, 'pending'
    )";

    if (mysqli_query($connection, $query)) {
        $_SESSION['order_id'] = mysqli_insert_id($connection);
        $_SESSION['amount'] = $totalAmount;
        
        if ($paymentMethod == 'debit_card') {
            header("Location: payment.php");
            exit();
        } else {
            header("Location: thankyou.html");
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <style>
    body {
        background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); /* Light blue gradient */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .container {
        width: 100%;
        max-width: 800px;
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white for container */
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px); /* Adds a blur effect to the background */
    }

    h2 {
        text-align: center;
        color: #1e3c72;
        margin-bottom: 30px;
        font-size: 2.2em;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        padding: 15px;
        border: 1px solid #e0e0e0;
    }

    th {
        text-align: left;
        background-color: #f8f9fa;
        color: #1e3c72;
        width: 30%;
        font-weight: 600;
    }

    td {
        width: 70%;
        background-color: white;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    input[type="date"],
    select {
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #1e3c72;
        outline: none;
        box-shadow: 0 0 5px rgba(30, 60, 114, 0.2);
    }

    input[type="submit"] {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
    }

    input[type="submit"]:active {
        transform: translateY(0);
    }

    .register-link {
        text-align: center;
        margin-top: 20px;
        color: white;
    }

    .register-link a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .register-link a:hover {
        color: #a3bffa;
        text-decoration: underline;
    }

    select#itemType {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 1em;
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    select#itemType:focus {
        border-color: #1e3c72;
        outline: none;
        box-shadow: 0 0 5px rgba(30, 60, 114, 0.2);
    }

    optgroup {
        font-weight: bold;
        color: #1e3c72;
    }

    option {
        font-weight: normal;
        color: #333;
        padding: 8px;
    }

    .nav-links .logout-btn {
        color: white;
        text-decoration: none;
        font-size: 16px;
        padding: 10px 15px;
        transition: background 0.3s, border-radius 0.3s;
        background: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
    }
    
    .nav-links .logout-btn:hover {
        background: #00a4ea;
        border-radius: 5px;
    }

    /* Add these navbar styles to your existing <style> section */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px 20px;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .navbar h2 {
        color: white;
        margin: 0;
        padding-left: 20px;
    }

    .nav-links {
        list-style: none;
        display: flex;
        margin: 0;
        padding: 0;
        padding-right: 20px;
    }

    .nav-links li {
        margin: 0 15px;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        padding: 10px 15px;
        transition: background 0.3s, border-radius 0.3s;
    }

    .nav-links a:hover {
        background: #00a4ea;
        border-radius: 5px;
    }

    /* Adjust the container to account for fixed navbar */
    .container {
        margin-top: 80px; /* Add top margin to prevent overlap with fixed navbar */
    }

    /* Update body style to accommodate fixed navbar */
    body {
        padding-top: 60px;
    }
</style>
</head>
<body>
    <nav class="navbar">
        <h2>Welcome to Tumble Dry</h2>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Order</h2>
        <form method="post" action="">

            <table>
                <tr>
                    <th><label for="fullname">Full Name:</label></th>
                    <td>
                        <input type="text" 
                               id="fullname" 
                               name="fullname" 
                               required 
                               placeholder="Enter your full name (e.g., chetan Ranjit Gosavi)">
                    </td>
                </tr>
                <tr>
                    <th><label for="email">Email:</label></th>
                    <td>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required 
                               placeholder="Enter your email address (e.g., chetan01@example.com)">
                    </td>
                </tr>
                <tr>
                    <th><label for="phone">Phone Number:</label></th>
                    <td>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               required 
                               maxlength="10" 
                               placeholder="Enter 10 digit mobile number">
                    </td>
                </tr>
                <tr>
                    <th><label for="address">Address:</label></th>
                    <td>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               required 
                               placeholder="Enter your complete address with street, city and pincode">
                    </td>
                </tr>
                <tr>
                    <th><label for="itemType">Item Type:</label></th>
                    <td>
                        <select id="itemType" 
                                name="itemType" 
                                required 
                                class="form-control">
                            <option value="" disabled selected>Select item type</option>
                            <optgroup label="Clothing">
                                <option value="shirts">Shirts</option>
                                <option value="trousers">Trousers</option>
                                <option value="jackets">Jackets</option>
                                <option value="dresses">Dresses</option>
                                <option value="saree">Saree</option>
                                <option value="special_occasion">Special Occasion Wear</option>
                            </optgroup>
                            <optgroup label="Home Textiles">
                                <option value="curtains">Curtains</option>
                                <option value="blankets">Blankets</option>
                                <option value="upholstery">Upholstery</option>
                            </optgroup>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="numItems">Number of Items:</label></th>
                    <td>
                        <input type="number" 
                               id="numItems" 
                               name="numItems" 
                               required 
                               min="1" 
                               placeholder="Enter total number of items">
                    </td>
                </tr>
                <tr>
                    <th><label for="service">Service:</label></th>
                    <td>
                        <select id="service" name="service" required>
                            <option value="" disabled selected>Select service type</option>
                            <option value="dry_clean">Dry Cleaning</option>
                            <option value="laundry">Wash and Laundry</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="pickupDate">Pick-Up Date:</label></th>
                    <td>
                        <input type="date" 
                               id="pickupDate" 
                               name="pickupDate" 
                               required 
                               placeholder="Select pickup date">
                    </td>
                </tr>
                <tr>
                    <th><label for="deliveryDate">Delivery Date:</label></th>
                    <td>
                        <input type="date" 
                               id="deliveryDate" 
                               name="deliveryDate" 
                               required 
                               placeholder="Select delivery date">
                        <small style="color: #666; display: block; margin-top: 5px;">
                            (Select a date between 5-7 days after pickup)
                        </small>
                    </td>
                </tr>
                <tr>
                    <th><label for="deliveryAddress">Delivery Address (if different):</label></th>
                    <td>
                        <input type="text" 
                               id="deliveryAddress" 
                               name="deliveryAddress" 
                               placeholder=" same as pickup address">
                    </td>
                </tr>
                <tr>
                    <th><label for="paymentMethod">Payment Method:</label></th>
                    <td>
                        <select id="paymentMethod" name="paymentMethod" required>
                            <option value="" disabled selected>Choose payment method</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="cash">Cash on Delivery</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <label>
                            <input type="checkbox" name="confirmation" required>
                            I confirm that the details provided are accurate and I agree to the terms and conditions.
                        </label>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td><input type="submit" value="Submit" class="submit-btn" name="submit"></td>
                </tr>
            </table>
        </form>
    </div>
    <script>
// Email validation function
function validateEmail(email) {
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailPattern.test(email);
}

// Phone validation function
function validatePhone(phone) {
    const phonePattern = /^\d{10}$/;
    return phonePattern.test(phone);
}

// Add event listeners for real-time validation
document.getElementById('email').addEventListener('input', function() {
    const emailInput = this;
    const emailError = document.getElementById('email-error');
    
    if (!validateEmail(emailInput.value)) {
        emailInput.style.borderColor = 'red';
        if (!emailError) {
            const errorMsg = document.createElement('small');
            errorMsg.id = 'email-error';
            errorMsg.style.color = 'red';
            errorMsg.textContent = 'Please enter a valid email address';
            emailInput.parentNode.appendChild(errorMsg);
        }
    } else {
        emailInput.style.borderColor = '#2a5298';
        if (emailError) {
            emailError.remove();
        }
    }
});

document.getElementById('phone').addEventListener('input', function() {
    const phoneInput = this;
    const phoneError = document.getElementById('phone-error');
    
    // Remove any non-digit characters
    phoneInput.value = phoneInput.value.replace(/\D/g, '');
    
    if (!validatePhone(phoneInput.value)) {
        phoneInput.style.borderColor = 'red';
        if (!phoneError) {
            const errorMsg = document.createElement('small');
            errorMsg.id = 'phone-error';
            errorMsg.style.color = 'red';
            errorMsg.textContent = 'Phone number must be 10 digits';
            phoneInput.parentNode.appendChild(errorMsg);
        }
    } else {
        phoneInput.style.borderColor = '#2a5298';
        if (phoneError) {
            phoneError.remove();
        }
    }
});

// Form submission validation
document.querySelector('form').addEventListener('submit', function(event) {
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    let isValid = true;
    
    if (!validateEmail(email)) {
        event.preventDefault();
        isValid = false;
        document.getElementById('email').focus();
    }
    
    if (!validatePhone(phone)) {
        event.preventDefault();
        isValid = false;
        if (isValid) { // Only focus if email was valid
            document.getElementById('phone').focus();
        }
    }
});

document.getElementById('pickupDate').addEventListener('change', function() {
    // Get the pickup date value
    let pickupDate = new Date(this.value);
    
    // Calculate minimum delivery date (5 days after pickup)
    let minDeliveryDate = new Date(pickupDate);
    minDeliveryDate.setDate(minDeliveryDate.getDate() + 5);
    
    // Calculate maximum delivery date (7 days after pickup)
    let maxDeliveryDate = new Date(pickupDate);
    maxDeliveryDate.setDate(maxDeliveryDate.getDate() + 7);
    
    // Format the dates to YYYY-MM-DD
    let minFormatted = minDeliveryDate.toISOString().split('T')[0];
    let maxFormatted = maxDeliveryDate.toISOString().split('T')[0];
    
    // Get delivery date input
    let deliveryInput = document.getElementById('deliveryDate');
    
    // Enable the delivery date input
    deliveryInput.removeAttribute('disabled');
    deliveryInput.removeAttribute('readonly');
    
    // Set min and max constraints
    deliveryInput.setAttribute('min', minFormatted);
    deliveryInput.setAttribute('max', maxFormatted);
    
    // Set default value to minimum delivery date
    deliveryInput.value = minFormatted;
});

// Set minimum date for pickup date to today
let today = new Date().toISOString().split('T')[0];
document.getElementById('pickupDate').setAttribute('min', today);

// Initially disable delivery date until pickup date is selected
document.getElementById('deliveryDate').disabled = true;

// Add this to your existing JavaScript section
const itemPrices = {
    'shirts': 40,
    'trousers': 60,
    'jackets': 40,
    'dresses': 80,
    'saree': 80,
    'special_occasion': 100,
    'curtains': 100,
    'blankets': 150,
    'upholstery': 80
};

// Add price display row after number of items
document.getElementById('itemType').addEventListener('change', function() {
    const selectedItem = this.value;
    const numItems = document.getElementById('numItems').value;
    updatePrice(selectedItem, numItems);
});

document.getElementById('numItems').addEventListener('input', function() {
    const selectedItem = document.getElementById('itemType').value;
    const numItems = this.value;
    updatePrice(selectedItem, numItems);
});

function updatePrice(itemType, numItems) {
    const pricePerItem = itemPrices[itemType] || 0;
    const totalPrice = pricePerItem * numItems;
    
    // Update or create price display
    let priceDisplay = document.getElementById('priceDisplay');
    if (!priceDisplay) {
        const numItemsRow = document.getElementById('numItems').closest('tr');
        const priceRow = document.createElement('tr');
        priceRow.innerHTML = `
            <th>Estimated Price:</th>
            <td id="priceDisplay" style="font-weight: bold; color: #194376;">
                ₹${totalPrice}
            </td>
        `;
        numItemsRow.parentNode.insertBefore(priceRow, numItemsRow.nextSibling);
    } else {
        priceDisplay.textContent = `₹${totalPrice}`;
    }
}
</script>
</body>
</html>

