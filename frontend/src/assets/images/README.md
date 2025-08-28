# Background Images Setup

## Stadium Crowd Background

### Instructions for `stadium-crowd.jpg`:

1. **Save your stadium image** as `stadium-crowd.jpg` in this directory (`frontend/src/assets/images/`)

2. **Replace the placeholder file** - The current `stadium-crowd.jpg` is just a placeholder text file. Replace it with your actual stadium crowd image.

3. **Image Requirements:**
   - **Format:** JPG, PNG, or WebP
   - **Recommended Size:** 1920x1080 or higher for best quality
   - **File Size:** Optimize to under 500KB for faster loading
   - **Content:** The stadium crowd image with blue lighting and packed stands that you provided

4. **Usage:**
   - **Home page welcome section** - Main hero background
   - Styled with blue/green gradient overlay to complement stadium colors

## Concert Background (Legacy)

### Instructions for `concert-background.jpg`:

- Still used for:
  - **Validate ticket page** - Background with blur effect
  - **Authentication pages** - Login and register backgrounds

## Current Implementation

The images are referenced in:
- `HomeView.vue` - Stadium crowd as hero section background
- `ValidateTicketView.vue` - Concert background with blur effect
- `LoginView.vue` - Concert background for auth
- `RegisterView.vue` - Concert background for auth

All pages are designed to work beautifully with event/stadium imagery and feature appropriate overlays for optimal text readability!
