<div class="container my-5">
    <h2>Contact Us</h2>
    <form method="POST" action="/contact">
        <div class="row">
            <div class="col-md-6 mb-3"><label>First Name</label><input name="first_name" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label>Last Name</label><input name="last_name" class="form-control" required></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label>Contact Number</label><input name="phone" class="form-control"></div>
            <div class="col-md-6 mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
        </div>
        <div class="mb-3"><label>Message</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
        <button class="btn btn-primary">Send Message</button>
    </form>
</div>
