<?php
    session_start();
    $title = "ItineraEase | Contact";
    $style = "contact.css";
?>
    <?php include 'header.php'; ?>
    <div class="page-header">
        <h1 class="page-header-title">Contact</h1>
        <p class="page-header-subtitle">Drop a message! We'd love to hear from you.</p>
    </div>
    <div id="form" class="content">
        <form name = "myForm" action="#">
            <div class="container">
                <figure><img src="images/roadtripimg.webp" alt="road trip image"></figure>
                <figcaption class="img--caption"> <em>"If you don't know where you are going, any road will get you there"</em> </figcaption>
            </div>   
            <div class="contact">
                <label for="first-name">First Name:</label><br>
                <input type="text" placeholder="Your first name" id="first-name" name="first-name" required/><br>
                <label for="last-name">Last Name:</label><br>
                <input type="text" placeholder="Your last name" id="last-name" name="last-name" required/><br>
                <label for="email">Email:</label><br>
                <input type="text" placeholder="Your email" id="email" name="email" required/><br>
                <label for="contact-reason">Reason for Contacting:</label><br>
                <select id="contact-reason" name="contact-reason" required>
                    <option value="">Please choose an option</option>
                    <option value="inquiry">General Inquiry</option>
                    <option value="feedback">Feedback</option>
                    <option value="support">Support</option>
                </select><br>
                <label for="message">Message:</label><br>
                <textarea id="message" name="message" rows="13" cols="65" placeholder="Write something..."></textarea><br>
                <input type="submit" value="Submit" id="submit-button" />
            </div>
        </form> 
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>