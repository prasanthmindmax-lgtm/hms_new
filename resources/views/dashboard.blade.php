<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<style>
  .hms-welcome-hero-wrap {
    margin-bottom: 1.5rem;
  }

  .hms-welcome-hero {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    padding: 2.25rem 2.5rem 2.5rem;
    min-height: 168px;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: #fff;
    box-shadow: 0 14px 44px rgba(109, 90, 205, 0.28);
    background:
      radial-gradient(ellipse 90% 120% at 12% 110%, rgba(255, 255, 255, 0.22) 0%, transparent 52%),
      radial-gradient(ellipse 70% 90% at 88% -10%, rgba(255, 255, 255, 0.18) 0%, transparent 48%),
      radial-gradient(ellipse 55% 70% at 55% 100%, rgba(255, 255, 255, 0.12) 0%, transparent 45%),
      linear-gradient(108deg, #6d5acd 0%, #7b6ad4 22%, #8f9ae8 45%, #a8c4f2 78%, #c9ddf8 100%);
  }

  .hms-welcome-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 120' preserveAspectRatio='none'%3E%3Cpath fill='rgba(255,255,255,0.07)' d='M0,64 C280,20 420,100 720,48 C980,4 1120,88 1440,40 L1440,120 L0,120 Z'/%3E%3Cpath fill='rgba(255,255,255,0.05)' d='M0,88 C240,120 480,40 720,72 C960,104 1180,56 1440,80 L1440,120 L0,120 Z'/%3E%3C/svg%3E") center bottom / 100% 55% no-repeat;
    pointer-events: none;
  }

  .hms-welcome-hero-inner {
    position: relative;
    z-index: 1;
    text-align: left;
    max-width: 42rem;
  }

  .hms-welcome-hero-inner h1 {
    font-size: clamp(1.35rem, 2.5vw, 1.75rem);
    font-weight: 700;
    margin: 0 0 0.65rem 0;
    letter-spacing: -0.02em;
    color: #fff;
    line-height: 1.25;
  }

  .hms-welcome-hero-inner .hms-welcome-greet {
    font-size: 0.9rem;
    font-weight: 500;
    margin: 0 0 0.85rem 0;
    opacity: 0.95;
    color: rgba(255, 255, 255, 0.98);
  }

  .hms-welcome-hero-inner .hms-welcome-lead {
    font-size: 0.95rem;
    line-height: 1.55;
    margin: 0;
    font-weight: 400;
    color: rgba(255, 255, 255, 0.92);
  }

  .hms-welcome-hero-inner .hms-welcome-lead+.hms-welcome-lead {
    margin-top: 0.4rem;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.9rem;
  }
</style>

<body>
  <div class="page-loader">
    <div class="bar"></div>
  </div>
  @include('superadmin.superadminnav')
  @include('superadmin.superadminheader')

  <div class="pc-container">
    <div class="pc-content">
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="m-b-10">Dashboard</h2>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="hms-welcome-hero-wrap">
            <div class="hms-welcome-hero">
              <div class="hms-welcome-hero-inner">
                <h1>Welcome to HMS 👋</h1>
                @isset($admin)
                <p class="hms-welcome-greet">Hello, {{ $admin->user_fullname ?? $admin->name ?? $admin->username ?? 'User' }}</p>
                @endisset
                <p class="hms-welcome-lead">Your centralized system for managing all operations.</p>
                <p class="hms-welcome-lead">Dashboard insights will be available soon.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('superadmin.superadminfooter')
</body>

</html>
