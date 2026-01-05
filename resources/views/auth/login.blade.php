@extends('layouts.app')

@section('title', 'Login - Perpustakaan IA')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="login-card animate__animated animate__fadeIn">
            <div class="card-header bg-danger text-white text-center py-4">
                <div class="logo mb-3">
                    <i class="fas fa-book-open fa-3x text-warning"></i>
                </div>
                <h3 class="mb-0">Login Sistem Perpustakaan</h3>
                <p class="mb-0 mt-2 opacity-75">Masukkan kredensial Anda untuk mengakses sistem</p>
            </div>
            <div class="card-body p-5">
                <form action="{{ route('login.process') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="username" class="form-label fw-bold">
                            <i class="fas fa-user text-danger me-2"></i>
                            Username / NIM
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-danger text-white">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="Masukkan username atau NIM" required
                                   value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">
                            <i class="fas fa-lock text-danger me-2"></i>
                            Password
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-danger text-white">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Masukkan password" required>
                            <button class="btn btn-outline-danger" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tambahkan alert untuk error/success -->
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Ingat saya pada perangkat ini
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> Login Sekarang
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="text-danger text-decoration-none">
                            <i class="fas fa-key me-1"></i> Lupa password?
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <p class="mb-0">
                    Belum punya akun?
                    <a href="#" class="text-danger fw-bold text-decoration-none">
                        Hubungi Administrator
                    </a>
                </p>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="card border-danger mt-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Akses Demo (Klik untuk mengisi otomatis)
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light cursor-pointer demo-credential"
                             data-username="admin"
                             data-password="admin123">
                            <div class="card-body text-center">
                                <h6 class="text-danger fw-bold">Administrator</h6>
                                <p class="mb-1"><small>Full access</small></p>
                                <span class="badge bg-danger">Klik untuk login</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light cursor-pointer demo-credential"
                             data-username="petugas"
                             data-password="petugas123">
                            <div class="card-body text-center">
                                <h6 class="text-danger fw-bold">Petugas</h6>
                                <p class="mb-1"><small>Moderator access</small></p>
                                <span class="badge bg-danger">Klik untuk login</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light cursor-pointer demo-credential"
                             data-username="anggota"
                             data-password="anggota123">
                            <div class="card-body text-center">
                                <h6 class="text-danger fw-bold">Anggota</h6>
                                <p class="mb-1"><small>Basic access</small></p>
                                <span class="badge bg-danger">Klik untuk login</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Auto-focus on username field
    const usernameField = document.getElementById('username');
    if (usernameField) {
        usernameField.focus();
    }

    // Demo credentials auto-fill
    const demoButtons = document.querySelectorAll('.demo-credential');
    demoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const username = this.getAttribute('data-username');
            const password = this.getAttribute('data-password');

            document.getElementById('username').value = username;
            document.getElementById('password').value = password;

            // Show notification
            showToast(`Kredensial ${username} telah dimasukkan`, 'info');
        });
    });
});

function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');

    const icon = type === 'success' ? 'check-circle' :
                 type === 'warning' ? 'exclamation-triangle' :
                 type === 'danger' ? 'times-circle' : 'info-circle';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${icon} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush
