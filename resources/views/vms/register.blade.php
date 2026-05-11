<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Visitor Registration — {{ $settings['hospital_name'] ?? "Dr. Aravind's IVF" }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.5.0/tabler-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --accent:#1a7f64;--accent2:#16a37e;--nav:#0f2d4a;
  --bg:#f0f4f8;--card:#fff;--border:#e2e8f0;
  --text:#1e293b;--muted:#64748b;
}
body{background:linear-gradient(135deg,#0f2d4a 0%,#1a7f64 100%);min-height:100vh;font-family:'Inter',system-ui,sans-serif;padding:20px 16px 40px}

.reg-wrapper{max-width:640px;margin:0 auto}

.reg-header{text-align:center;padding:24px 0 20px}
.reg-header .hosp-logo{width:56px;height:56px;background:#1a7f64;border:3px solid rgba(255,255,255,0.3);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px}
.reg-header .hosp-logo i{font-size:28px;color:#fff}
.reg-header h1{font-size:20px;font-weight:700;color:#fff;margin-bottom:4px}
.reg-header p{font-size:13px;color:rgba(255,255,255,0.7)}

/* Steps */
.step-nav{display:flex;justify-content:center;gap:0;margin-bottom:24px}
.step-item{display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;max-width:120px;position:relative}
.step-item:not(:last-child)::after{content:'';position:absolute;top:16px;left:calc(50% + 16px);width:calc(100% - 32px);height:2px;background:rgba(255,255,255,0.25)}
.step-item.done::after,.step-item.active::after{background:rgba(255,255,255,0.7)}
.step-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;border:2px solid rgba(255,255,255,0.3);color:rgba(255,255,255,0.5);background:rgba(255,255,255,0.1);transition:all 0.3s}
.step-item.done .step-dot{background:rgba(255,255,255,0.9);color:var(--accent);border-color:transparent}
.step-item.active .step-dot{background:#fff;color:var(--accent);border-color:transparent;box-shadow:0 0 0 4px rgba(255,255,255,0.2)}
.step-label{font-size:10px;color:rgba(255,255,255,0.55);font-weight:500;text-transform:uppercase;letter-spacing:0.06em}
.step-item.active .step-label,.step-item.done .step-label{color:rgba(255,255,255,0.9)}

/* Card */
.reg-card{background:#fff;border-radius:20px;padding:24px 28px;box-shadow:0 20px 60px rgba(0,0,0,0.25)}
@media(max-width:480px){.reg-card{padding:18px 16px}}

.section-heading{font-size:12px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em;padding-bottom:8px;border-bottom:2px solid #f0fdf4;margin-bottom:18px}

.form-label{font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px}
.form-label .req{color:#dc2626;margin-left:2px}
.form-control,.form-select{font-size:13px;border:1.5px solid var(--border);border-radius:10px;padding:9px 12px;color:var(--text);transition:border-color 0.15s}
.form-control:focus,.form-select:focus{border-color:var(--accent2);box-shadow:0 0 0 3px rgba(22,163,126,0.12);outline:none}
.form-text{font-size:11px;color:var(--muted);margin-top:4px}

/* Photo upload */
.photo-upload{border:2px dashed var(--border);border-radius:12px;padding:20px;text-align:center;cursor:pointer;transition:all 0.15s;background:var(--bg)}
.photo-upload:hover{border-color:var(--accent2);background:#f0fdf4}
.photo-upload i{font-size:32px;color:var(--muted);margin-bottom:8px}
.photo-upload p{font-size:12px;color:var(--muted)}
.photo-preview{width:80px;height:80px;object-fit:cover;border-radius:10px;margin:8px auto 0;display:none;border:2px solid var(--accent)}

/* Declaration */
.decl-box{background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:14px 16px}
.decl-box p{font-size:12px;color:var(--muted);line-height:1.6}

/* Buttons */
.btn-next{width:100%;background:linear-gradient(135deg,#1a7f64,#16a37e);color:#fff;border:none;border-radius:12px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;transition:all 0.2s;margin-top:8px}
.btn-next:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(26,127,100,0.35)}
.btn-back{background:var(--bg);color:var(--text);border:1.5px solid var(--border);border-radius:12px;padding:11px 20px;font-size:13px;font-weight:600;cursor:pointer;width:100%;transition:all 0.15s}
.btn-back:hover{background:var(--border)}

.error-msg{color:#dc2626;font-size:11px;margin-top:4px;display:none}

/* Steps pages */
.step-page{display:none}
.step-page.active{display:block}
</style>
</head>
<body>

<div class="reg-wrapper">

  {{-- Header --}}
  <div class="reg-header">
    <div class="hosp-logo"><i class="ti ti-building-hospital"></i></div>
    <h1>{{ $settings['hospital_name'] ?? "Dr. Aravind's IVF & Pregnancy Centre" }}</h1>
    <p><i class="ti ti-map-pin" style="font-size:13px"></i>
      @if($qr->location) {{ $qr->location }} — @endif
      {{ $qr->branch ?? ($settings['default_branch'] ?? 'Main Hospital') }}
    </p>
  </div>

  {{-- Step nav --}}
  <div class="step-nav" id="stepNav">
    <div class="step-item active" id="step-nav-1">
      <div class="step-dot"><i class="ti ti-user" style="font-size:14px"></i></div>
      <div class="step-label">Personal</div>
    </div>
    <div class="step-item" id="step-nav-2">
      <div class="step-dot">2</div>
      <div class="step-label">Visit Info</div>
    </div>
    <div class="step-item" id="step-nav-3">
      <div class="step-dot">3</div>
      <div class="step-label">Confirm</div>
    </div>
  </div>

  <div class="reg-card">
    <form id="regForm" method="POST" action="{{ route('vms.register.store', $qr->uuid) }}" enctype="multipart/form-data">
      @csrf

      {{-- STEP 1: Personal Info --}}
      <div class="step-page active" id="step-1">
        <div class="section-heading">A. Personal Information</div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input type="text" name="visitor_name" id="visitor_name" class="form-control" placeholder="Enter your full name" required autocomplete="name">
            <div class="error-msg" id="err_name">Please enter your full name</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Mobile Number <span class="req">*</span></label>
            <input type="tel" name="visitor_phone" id="visitor_phone" class="form-control" placeholder="10-digit mobile number" maxlength="15" required autocomplete="tel">
            <div class="error-msg" id="err_phone">Please enter a valid phone number</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address</label>
            <input type="email" name="visitor_email" class="form-control" placeholder="optional@email.com" autocomplete="email">
          </div>
          <div class="col-md-6">
            <label class="form-label">ID Type</label>
            <select name="id_type" class="form-select">
              <option value="">Select ID type</option>
              <option>Aadhaar Card</option>
              <option>Driving License</option>
              <option>Passport</option>
              <option>Voter ID</option>
              <option>Employee ID</option>
              <option>Other</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">ID Number</label>
            <input type="text" name="id_number" class="form-control" placeholder="ID card number">
          </div>
          <div class="col-12">
            <label class="form-label">Photo <span style="color:var(--muted)">(optional)</span></label>
            <div class="photo-upload" onclick="$('#photoInput').click()">
              <i class="ti ti-camera"></i>
              <p>Tap to capture / upload photo</p>
              <img id="photoPreview" class="photo-preview" alt="Preview">
            </div>
            <input type="file" id="photoInput" name="photo" accept="image/*" capture="user" style="display:none">
          </div>
        </div>
        <button type="button" class="btn-next mt-4" onclick="goStep(2)">
          Continue <i class="ti ti-arrow-right" style="font-size:16px"></i>
        </button>
      </div>

      {{-- STEP 2: Visit Details --}}
      <div class="step-page" id="step-2">
        <div class="section-heading">B. Visit Details</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Visitor Type <span class="req">*</span></label>
            <select name="visitor_type" id="visitor_type" class="form-select" required>
              <option value="">Select type</option>
              <option value="pharma">Pharma Vendor</option>
              <option value="non_pharma">Non-Pharma Vendor</option>
              <option value="patient_relative">Patient Relative</option>
              <option value="job_applicant">Job Applicant</option>
              <option value="government">Government Official</option>
              <option value="other">Other</option>
            </select>
            <div class="error-msg" id="err_type">Please select visitor type</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Company / Organisation</label>
            <input type="text" name="company_name" class="form-control" placeholder="Company or hospital name">
          </div>
          <div class="col-12">
            <label class="form-label">Purpose of Visit <span class="req">*</span></label>
            <select name="purpose" id="purpose" class="form-select" required>
              <option value="">Select purpose</option>
              <option>Product Detailing</option>
              <option>Sample Delivery</option>
              <option>Equipment Service</option>
              <option>Equipment Demo</option>
              <option>Patient Visit</option>
              <option>Interview / Job</option>
              <option>Vendor Meeting</option>
              <option>Delivery</option>
              <option>Government Inspection</option>
              <option>Other</option>
            </select>
            <div class="error-msg" id="err_purpose">Please select purpose</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Person / Doctor to Meet</label>
            <select name="person_to_meet" class="form-select">
              <option value="">Select (optional)</option>
              @foreach($doctors as $doc)
              <option>{{ $doc }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <select name="department" class="form-select">
              <option value="">Select department</option>
              @foreach($departments as $dept)
              <option>{{ $dept }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Appointment Time</label>
            <input type="time" name="appointment_time" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Equipment / Samples Carried</label>
            <input type="text" name="equipment_carried" class="form-control" placeholder="e.g. Tablet samples, Brochures, Tools…">
          </div>
        </div>
        <div class="row g-3 mt-1">
          <div class="col-6">
            <button type="button" class="btn-back" onclick="goStep(1)">← Back</button>
          </div>
          <div class="col-6">
            <button type="button" class="btn-next" onclick="goStep(3)">Continue →</button>
          </div>
        </div>
      </div>

      {{-- STEP 3: Confirm --}}
      <div class="step-page" id="step-3">
        <div class="section-heading">C. Confirm & Submit</div>

        {{-- Summary --}}
        <div id="summaryBox" style="background:var(--bg);border-radius:12px;padding:16px;margin-bottom:20px">
          {{-- filled by JS --}}
        </div>

        <div class="decl-box mb-4">
          <div class="d-flex align-items-start gap-2">
            <input type="checkbox" name="declaration" id="decl" style="width:16px;height:16px;margin-top:3px;accent-color:var(--accent)" required>
            <label for="decl" style="font-size:12px;color:var(--muted);line-height:1.6;cursor:pointer">
              I hereby declare that all information provided above is true and accurate.
              I agree to comply with all hospital security guidelines, visitor policies, and regulations of
              <strong style="color:var(--text)">{{ $settings['hospital_name'] ?? "Dr. Aravind's IVF & Pregnancy Centre" }}</strong>
              during my visit.
            </label>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-5">
            <button type="button" class="btn-back" onclick="goStep(2)">← Back</button>
          </div>
          <div class="col-7">
            <button type="submit" class="btn-next" id="submitBtn">
              <i class="ti ti-send" style="font-size:16px"></i> Submit Registration
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div style="text-align:center;margin-top:16px;font-size:11px;color:rgba(255,255,255,0.4)">
    Secure visitor registration — {{ $settings['hospital_name'] ?? '' }}
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let currentStep = 1;

function goStep(target) {
  // Validate current step
  if(target > currentStep && !validateStep(currentStep)) return;

  // Update step nav
  for(let i = 1; i <= 3; i++) {
    const nav = document.getElementById(`step-nav-${i}`);
    nav.classList.remove('active','done');
    if(i < target) nav.classList.add('done');
    else if(i === target) nav.classList.add('active');
    // update dot text for done steps
    if(i < target) nav.querySelector('.step-dot').innerHTML = '<i class="ti ti-check" style="font-size:14px"></i>';
    else if(i === 2) nav.querySelector('.step-dot').textContent = i;
    else if(i === 3) nav.querySelector('.step-dot').textContent = i;
  }

  // Hide/show pages
  document.querySelectorAll('.step-page').forEach(p => p.classList.remove('active'));
  document.getElementById(`step-${target}`).classList.add('active');

  // If step 3, build summary
  if(target === 3) buildSummary();

  currentStep = target;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
  if(step === 1) {
    const name = $('#visitor_name').val().trim();
    const phone = $('#visitor_phone').val().trim();
    let ok = true;
    if(!name){ $('#err_name').show(); ok=false; } else $('#err_name').hide();
    if(!phone || phone.length < 10){ $('#err_phone').show(); ok=false; } else $('#err_phone').hide();
    return ok;
  }
  if(step === 2) {
    const type = $('#visitor_type').val();
    const purpose = $('#purpose').val();
    let ok = true;
    if(!type){ $('#err_type').show(); ok=false; } else $('#err_type').hide();
    if(!purpose){ $('#err_purpose').show(); ok=false; } else $('#err_purpose').hide();
    return ok;
  }
  return true;
}

function buildSummary() {
  const data = {
    Name:    $('#visitor_name').val(),
    Phone:   $('#visitor_phone').val(),
    Type:    $('#visitor_type option:selected').text(),
    Company: $('[name=company_name]').val() || '—',
    Purpose: $('#purpose option:selected').text(),
    Meeting: $('[name=person_to_meet] option:selected').text() || '—',
    Dept:    $('[name=department] option:selected').text() || '—',
  };
  let html = '';
  for(const [k,v] of Object.entries(data)) {
    html += `<div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:12px">
      <span style="color:var(--muted)">${k}</span>
      <span style="font-weight:600;color:var(--text)">${v}</span>
    </div>`;
  }
  $('#summaryBox').html(html);
}

// Photo preview
$('#photoInput').on('change', function() {
  const file = this.files[0];
  if(file) {
    const reader = new FileReader();
    reader.onload = e => { $('#photoPreview').attr('src', e.target.result).show(); };
    reader.readAsDataURL(file);
  }
});

// Prevent double submit
$('#regForm').on('submit', function() {
  if(!$('#decl').is(':checked')) {
    alert('Please agree to the declaration to proceed.');
    return false;
  }
  $('#submitBtn').prop('disabled', true).text('Submitting…');
  return true;
});
</script>
</body>
</html>
