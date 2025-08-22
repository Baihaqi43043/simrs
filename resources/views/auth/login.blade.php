@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header')
    <div class="text-center mb-3">
        <h1 class="h3">
            <b>SIM</b>RS
        </h1>
        <p class="login-box-msg">Sistem Informasi Manajemen Rumah Sakit</p>
    </div>
@stop

@section('auth_body')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h6><i class="fas fa-exclamation-triangle"></i> Ada kesalahan:</h6>
            <ul class="mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Email"
                   required
                   autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Password"
                   required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="Ingat saya">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat Saya</label>
                </div>
            </div>

            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="fas fa-spinner fa-spin" style="display: none;" id="login-spinner"></span>
                    <span id="login-text">Masuk</span>
                </button>
            </div>
        </div>

    </form>

    {{-- Demo credentials info --}}
    <div class="mt-3">
        <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> Demo Credentials:</h5>
            <p class="mb-1"><strong>Email:</strong> admin@simrs.com</p>
            <p class="mb-0"><strong>Password:</strong> password</p>
            <button type="button" class="btn btn-sm btn-outline-info mt-2" id="demo-fill">
                <i class="fas fa-magic"></i> Auto Fill
            </button>
        </div>
    </div>
@stop

@section('auth_footer')
    <div class="text-center">
        <p class="mb-0">
            <small class="text-muted">
                &copy; {{ date('Y') }} SIMRS. Sistem Informasi Manajemen Rumah Sakit.
            </small>
        </p>
    </div>
@stop

@section('adminlte_js')
<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Loading animation on form submit
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const spinner = $('#login-spinner');
        const text = $('#login-text');

        submitBtn.prop('disabled', true);
        spinner.show();
        text.text('Memproses...');

        return true;
    });

    // Demo credential auto-fill
    $('#demo-fill').on('click', function(e) {
        e.preventDefault();
        $('input[name="email"]').val('admin@simrs.com');
        $('input[name="password"]').val('password');

        // Visual feedback
        $(this).html('<i class="fas fa-check"></i> Filled!');
        setTimeout(() => {
            $(this).html('<i class="fas fa-magic"></i> Auto Fill');
        }, 2000);
    });

    // Enter key handler for better UX
    $('input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });
});
</script>
@stop

@section('adminlte_css')
<style>
.login-box, .register-box {
    width: 400px;
    margin: 2% auto;
}

.login-logo, .register-logo {
    font-size: 2.1rem;
    font-weight: 300;
    margin-bottom: 0.9rem;
    text-align: center;
}

.login-box-msg, .register-box-msg {
    margin: 0;
    text-align: center;
    padding: 0 20px 20px 20px;
    color: #6c757d;
    font-size: 0.9rem;
}

.callout.callout-info {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.callout {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
    border-left: 4px solid #dee2e6;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.icheck-primary {
    margin-bottom: 0;
}

/* Custom hospital theme colors */
.btn-primary {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-primary:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-outline-info {
    color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-info:hover {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.login-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.card {
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
}

.card-header {
    background: transparent;
    border-bottom: none;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .login-box, .register-box {
        width: 90%;
        margin: 5% auto;
    }

    .callout {
        padding: 0.5rem 1rem;
    }

    .btn-block {
        font-size: 0.9rem;
    }
}

/* Animation for smooth transitions */
.alert {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn {
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>
@endsection
