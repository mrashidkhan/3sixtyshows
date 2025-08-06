@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <h1 class="mb-4">Privacy Policy</h1>

            <p>
                            <span class="cate">Spectacular Live Performances, Memorable Experiences</span>
                            Our live entertainment division forms the cornerstone of our operations. We excel in organizing and presenting dynamic Bollywood concerts and cultural celebrations, partnering with celebrated artists and performers from India. Our commitment to excellence drives us to work alongside industry-leading talent, creating spectacular productions that captivate and inspire our audiences. We transform conventional venues into vibrant cultural destinations where music, artistry, and tradition unite to forge lasting memories.
                        </p>

            <section class="mb-5">
                <h2 class="h4 mb-3">How We Use Your Information</h2>
                <p>The information we collect is used to deliver services, process transactions, improve user experience, and communicate with you about your account or our offerings.</p>
            </section>

            <section class="mb-5">
                <h2 class="h4 mb-3">Data Protection</h2>
                <p>We implement appropriate security measures to protect your personal information from unauthorized access or disclosure. However, no internet transmission is completely secure.</p>
            </section>

            <section class="mb-5">
                <h2 class="h4 mb-3">Third-Party Services</h2>
                <p>We may use third-party services that collect information to help us operate our business. These parties have their own privacy policies governing their use of data.</p>
            </section>

            <section class="mb-5">
                <h2 class="h4 mb-3">Policy Changes</h2>
                <p>We may update this policy periodically. Any changes will be posted on this page, and your continued use of our services constitutes acceptance of the updated policy.</p>
            </section>

            <div class="text-muted small">
                Last updated: {{ now()->format('F j, Y') }}
            </div>
        </div>
    </div>
</div>
@endsection
