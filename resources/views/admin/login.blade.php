<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pattoki Naturals!| </title>
    <!-- Bootstrap -->
    <link href="{{asset('admin_theme/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('admin_theme/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{asset('admin_theme/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{asset('admin_theme/vendors/animate.css/animate.min.css')}}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{asset('admin_theme/build/css/custom.min.css')}}" rel="stylesheet">
  </head>
  <body class="login">
      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            @if ($errors->any())
              <div class=alert alert-danger>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
              </div>
            @endif
            <form action="{{route('admin.makeLogin')}}" method="post">
              <h1>Login Form</h1>
              @csrf
              <div>
                <input type="text" class="form-control" name="email" placeholder="Username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" name="password" placeholder="Password" required="" />
              </div>
              <div class="">
                <input type="submit" name="" class="btn btn-primary btn-sm">
              </div>
              <div class="clearfix"></div>
              <div class="separator">
                <h2 class="change_link">Welcome to login !</h2>
                <div class="separator">
                <div class="clearfix"></div>
                <br />
              </div>
            </form>
          </section>
      </div>
    </div>
  </body>
</html>
