{{-- Change Password Modal --}}
@if (session('status') === 'password-updated')
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="passwordUpdatedToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">Password updated successfully.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toastEl = document.getElementById('passwordUpdatedToast');
    if (toastEl && typeof bootstrap !== 'undefined') {
        var toast = new bootstrap.Toast(toastEl, { delay: 4000, autohide: true });
        toast.show();
    }
});
</script>
@endif

<style>
/* Fix modal overflow issues */
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}

body:not(.modal-open) {
    overflow-x: hidden !important;
    overflow-y: auto !important;
    padding-right: 0 !important;
}

/* Password Input Wrapper */
.password-input-wrapper {
    position: relative;
    display: block;
}

/* Input with padding for icon */
.password-input-wrapper .form-control {
    padding-right: 50px !important; /* Make space for icon */
    width: 100%;
}

/* Eye Icon - Always Visible */
.toggle-password {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 20px;
    color: #6c757d;
    z-index: 999; /* High z-index to stay on top */
    pointer-events: auto;
    user-select: none;
    transition: color 0.2s ease;
    background: transparent;
    border: none;
    padding: 0;
    margin: 0;
}

.toggle-password:hover {
    color: #495057;
}

.toggle-password:active {
    color: #667eea;
}

/* Ensure icon stays visible */
.toggle-password i {
    display: block;
    pointer-events: none;
}

/* Modal Styling */
#changePasswordModal .modal-content {
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    border: none;
}

#changePasswordModal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px 24px;
    border: none;
}

#changePasswordModal .modal-header .modal-title {
    font-weight: 600;
    font-size: 18px;
}

#changePasswordModal .modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 1;
}

#changePasswordModal .modal-body {
    padding: 30px 24px;
}

#changePasswordModal .form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

#changePasswordModal .form-label i {
    margin-right: 6px;
    color: #667eea;
}

#changePasswordModal .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 15px;
    transition: all 0.2s ease;
    height: 48px;
}

#changePasswordModal .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

#changePasswordModal .form-control::placeholder {
    color: #9ca3af;
    font-size: 14px;
}

#changePasswordModal .alert {
    border-radius: 10px;
    border: none;
    padding: 14px 18px;
    background-color: #fee2e2;
    color: #991b1b;
}

#changePasswordModal .alert strong {
    display: block;
    margin-bottom: 6px;
}

#changePasswordModal .alert ul li {
    margin-left: 8px;
    margin-bottom: 4px;
}

#changePasswordModal .text-muted {
    font-size: 13px;
    color: #6b7280;
    display: inline-block;
    margin-top: 6px;
}

#changePasswordModal .modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
    background-color: #f9fafb;
    border-radius: 0 0 15px 15px;
}

#changePasswordModal .btn {
    padding: 12px 28px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.2s ease;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

#changePasswordModal .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

#changePasswordModal .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

#changePasswordModal .btn-secondary {
    background-color: #f3f4f6;
    color: #374151;
    border: 2px solid #e5e7eb;
}

#changePasswordModal .btn-secondary:hover {
    background-color: #e5e7eb;
    border-color: #d1d5db;
}
</style>

<div class="modal fade" id="changePasswordModal" tabindex="-1"
    aria-labelledby="changePasswordModalLabel" aria-hidden="true"
    data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-lock"></i> Change Password
                </h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="modal-body">

                    {{-- Error Display --}}
                    @if ($errors->hasBag('updatePassword') && $errors->updatePassword->any())
                        <div class="alert alert-danger mb-3">
                            <strong><i class="bi bi-exclamation-triangle"></i> Error:</strong>
                            <ul class="mb-0 list-unstyled">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Current Password --}}
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-lock-fill"></i>Current Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password"
                                class="form-control password-field"
                                name="current_password"
                                placeholder="Enter your current password"
                                required
                                autocomplete="current-password">
                            <button type="button" class="toggle-password" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-key-fill"></i>New Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password"
                                class="form-control password-field"
                                name="password"
                                placeholder="Enter your new password"
                                required
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Must be at least 8 characters
                        </small>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-check2-circle"></i>Confirm Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password"
                                class="form-control password-field"
                                name="password_confirmation"
                                placeholder="Confirm your new password"
                                required
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>Cancel
                    </button>
                    <button type="submit"
                        class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>Update Password
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@if ($errors->hasBag('updatePassword') && $errors->updatePassword->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('changePasswordModal');
    if (el) { 
        var modal = new bootstrap.Modal(el); 
        modal.show(); 
    }
});
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", function () {
    
    // Fix overflow issue when modal closes
    var changePasswordModal = document.getElementById('changePasswordModal');
    if (changePasswordModal) {
        changePasswordModal.addEventListener('hidden.bs.modal', function () {
            // Remove any modal-related classes and styles
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Remove modal backdrop if it exists
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        });
    }
    
    // Password Toggle Functionality
    document.querySelectorAll(".toggle-password").forEach(function (toggleBtn) {
        
        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Find the password input in the same wrapper
            const wrapper = this.closest('.password-input-wrapper');
            const input = wrapper.querySelector('.password-field');
            const icon = this.querySelector('i');
            
            if (input && icon) {
                if (input.type === "password") {
                    // Show password
                    input.type = "text";
                    icon.classList.remove("bi-eye-slash");
                    icon.classList.add("bi-eye");
                } else {
                    // Hide password
                    input.type = "password";
                    icon.classList.remove("bi-eye");
                    icon.classList.add("bi-eye-slash");
                }
            }
        });
        
    });
    
});
</script>