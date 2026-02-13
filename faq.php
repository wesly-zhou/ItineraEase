<?php
    session_start();
    $title = "ItineraEase | FAQ";
    $style = "faqstyles.css";
?>
    <?php include 'header.php'; ?>
    <div class="page-header">
        <h1 class="page-header-title">FAQ</h1>
        <p class="page-header-subtitle">Frequently Asked Questions</p>
    </div>
    <div class="faq-container">
        <div class="faq-image-box">
            <h1 class="faq-image-title">Have <br> questions?</h1>
            <img class="faq-image" src="images/faq-image.jpg">
        </div>
        <div class="faq-box">
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-1">
                <label for="faq-trigger-1" class="faq-title">How do I plan a road trip using the app?</label>
                <div class="faq-content">
                    <p>To plan a road trip using our app, simply navigate to the login section and create an account. From our home page, enter your desired starting point and destination. Select any waypoints or stops you'd like to include, and save the route once you're done. You can also look for things to do from our <a href="search.php">search</a> page. </p>
                </div>
            </div>
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-2">
                <label for="faq-trigger-2" class="faq-title">Does the app offer recommendations for attractions and restaurants along the way?</label>
                <div class="faq-content">
                    <p>Yes! Our map provides recommendations for attractions, restaurants, and other points of interest along your route, but we also encourage you to look for events a little further way and find noteworthy detours.</p>
                </div>
            </div>
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-3">
                <label for="faq-trigger-3" class="faq-title">Can I share my trip plan with friends and family?</label>
                <div class="faq-content">
                    <p>Unfortunately, our app does not currently support sharing trip plans with others. We plan to add collaborative trip planning as well as the ability for users to share their plans in the form of a generatable link, so please keep your eyes peeled!</p>
                </div>
            </div>
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-4">
                <label for="faq-trigger-4" class="faq-title">How do I access my saved trip plans?</label>
                <div class="faq-content">
                    <p>To access your saved trip plans, simply log in to your account and navigate to the "Saved Trips" section. Here, you'll find a list of all your saved trip plans, which you can view, edit, or delete as needed.</p>
                </div>
            </div>
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-5">
                <label for="faq-trigger-5" class="faq-title">Can I use this app to plan road trips across countries?</label>
                <div class="faq-content">
                    <p>Our app uses the Google Maps API to generate a route between the desired locations. Unfortunately, there may appear issues where the map is unable to generate a route across national borders. We deeply apologize for any inconvenience this may cause.</p>
                </div>
            </div>
            <div class="faq-wrapper">
                <input type="checkbox" class="faq-trigger" id="faq-trigger-6">
                <label for="faq-trigger-6" class="faq-title">How do I provide feedback or report an issue with the app?</label>
                <div class="faq-content">
                    <p>To provide feedback or report an issue with the app, navigate to the <a href="contact.php">Contact</a> page and fill out the feedback form. You can also send an email to our support team at
                        <a href="mailto: support@itineraease.com"> support@itineraease.com</a></p>
                </div>
            </div>           
        </div>
    </div>  
    <?php include 'footer.php'; ?>
</body>
</html>