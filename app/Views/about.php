<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Vertical Sidebar */
        .sidebar {
            background-color: #D6F2F8; /* Light blue */
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            overflow-y: auto;
            padding-top: 2rem;
        }

        .sidebar .nav-link {
            color: #000;
            font-weight: 500;
            margin: 10px 0;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            color: #007BFF;
        }

        /* Main Content */
        .content {
            margin-left: 200px; /* Leave space for sidebar */
            padding: 2rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 1rem 0;
            }

            .content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Vertical Sidebar -->
    <div class="sidebar">
        <h4 class="text-center fw-bold mb-4">NOTIPUS</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="#ourstory">Our Story</a>
            <a class="nav-link" href="#ourvision">Our Vision</a>
            <a class="nav-link" href="#ourmission">Our Mission</a>
            <a class="nav-link" href="#whatdowedo">What We Do?</a>
            <a class="nav-link" href="#howitworks">How it Works?</a>
            <a class="nav-link" href="#contactus">Contact Us</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <br id="ourstory">
        <br>
        <br>
<!-- Our Story -->
<section class="mb-5 d-flex align-items-center">
  <div style="flex: 0 0 auto; max-width: 400px; margin-right: 20px;">
    <img src="assets/imgs/about/our_story.JPG" class="img-fluid" alt="Our Story Image" style="width: 100%; height: auto;">
  </div>
  <div style="flex: 1;">
    <h1 class="text-start">Our Story</h1>
    <br>
    <p>
      NoTip.Us was born from a shared belief that dining experiences should be enjoyable and ethical for everyone involved—consumers, service workers, and business owners. We saw an opportunity to challenge the outdated tipping culture that often fosters inequity and income insecurity, especially for hardworking individuals in the hospitality industry.
    </p>
  </div>
</section>


<!-- Our Vision -->
<br id="ourvision">
<br>
<section class="mb-5 d-flex align-items-center">
  <div style="flex: 1; margin-right: 20px;">
    <h1 class="text-start">Our Vision</h1>
    <br>
    <p>
      We envision a world where tipping is no longer a necessity but an option—where workers in the hospitality industry are valued, respected, and fairly compensated without needing to rely on gratuities. Our goal is to inspire change in the industry, empowering businesses to adopt sustainable practices and fostering a culture of equity for all.
    </p>
  </div>
  <div style="flex: 0 0 auto; max-width: 400px;">
    <img src="assets/imgs/about/our_vision.JPG" class="img-fluid" alt="Our Vision Image" style="width: 100%; height: auto;">
  </div>
</section>


 <!-- Our Mission -->
<br id="ourmission">
<br>
<section class="mb-5 d-flex align-items-center">
  <div style="flex: 0 0 auto; max-width: 400px; margin-right: 20px;">
    <img src="assets/imgs/about/our_mission.JPG" class="img-fluid" alt="Our Mission Image" style="width: 100%; height: auto;">
  </div>
  <div style="flex: 1;">
    <h1 class="text-start">Our Mission</h1>
    <br>
    <p>
      Our mission is to reshape the dining experience by advocating for transparency, fairness, and ethical compensation practices. At NoTip.Us, we connect diners with establishments that:
      <br><br>
      1. Operate on a No-Tip Policy, ensuring their employees are paid a fair, stable wage without relying on tips.
      <br><br>
      2. Promote Fair-Tip Practices, setting reasonable tipping ranges or treating tips as a bonus rather than a necessity.
    </p>
  </div>
</section>


        <!-- What Do We Do? -->
        <br id="whatdowedo">
        <br>
        <section class="mb-5">
            <h1 class="text-start">What Do We Do?</h1>
            <br>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-4 bg-secondary text-white text-center">
                        <h4>Discovery Tools</h4>
                        <p>Our platform makes it easy to find No-Tip and Fair-Tip establishments near you. Search by location, cuisine, or tipping policy using interactive maps and lists.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-light text-center">
                        <h4>Contribution</h4>
                        <p>Users can contribute to the community by adding and verifying businesses which would help grow a trusted database of ethical establishments.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-light text-center">
                        <h4>Business Support</h4>
                        <p>We offer certification and promotional opportunities for restaurants that meet our criteria, helping them reach a larger audience.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-secondary text-white text-center">
                        <h4>Fairness Advocacy</h4>
                        <p>By choosing No-Tip and Fair-Tip establishments, you’re supporting a vision of ethical dining that puts workers’ well-being first.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <br id="howitworks">
        <br>
      <section class="mb-5">
  <h1 class="text-start">How It Works?</h1>
  <br>
  <div class="row text-center g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <img src="assets/imgs/about/search.JPG" class="img-fluid mb-3" alt="Search and Discover" style="max-height: 150px;">
          <h5 class="fw-bold">Search and Discover</h5>
          <p>Use our interactive tools to find establishments with No-Tip or Fair-Tip policies near you.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <img src="assets/imgs/about/verify.JPG" class="img-fluid mb-3" alt="Verify and Contribute" style="max-height: 150px;">
          <h5 class="fw-bold">Verify and Contribute</h5>
          <p>Add or verify businesses, ensuring our database remains accurate and trustworthy.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <img src="assets/imgs/about/connect.JPG" class="img-fluid mb-3" alt="Connect and Support" style="max-height: 150px;">
          <h5 class="fw-bold">Connect and Support</h5>
          <p>Engage with businesses that prioritize fairness and sustainability, promoting their ethical practices.</p>
        </div>
      </div>
    </div>
  </div>
</section>

    
        <!-- Join the Movement Section -->
        <br id="contactus">
        <section  class="my-5">
            <div class="p-4 bg-light border rounded">
                <h1 class="text-start mb-3">Join the Movement</h1>
                <p class="fs-5">
                    At NoTip.Us, we believe that small actions can lead to significant change. Every time you choose a 
                    No-Tip or Fair-Tip establishment, you’re taking a stand for transparency, equity, and sustainability 
                    in the hospitality industry. Together, we can create a future where dining is ethical, enjoyable, 
                    and empowering for everyone involved.
                </p>
                <p class="fw-bold mb-0">Contact Us: 
                    <a href="mailto:contact@notip.us" class="text-decoration-none">Contact@notip.us</a>
                </p>
            </div>
        </section>
        <br>
<!-- Footer -->
</section> 

<!-- Footer -->

<footer class="bg-dark text-white py-2" style="margin: 0; position: relative; z-index: 1; width: 100%;">
    <div class="text-center">
        <p class="mb-0">&copy; 2024 NoTip.Us. All Rights Reserved.</p>
    </div>
</footer>

    </div>
    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
