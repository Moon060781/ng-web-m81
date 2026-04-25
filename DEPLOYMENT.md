# GitHub Pages Deployment Guide

## Overview
This repository is configured for GitHub Pages deployment. The site is automatically deployed to:
**https://moon060781-bot.github.io/ng-web-m81**

## What's Included

### 1. **Our Masterpieces Portfolio Section**
A premium, interactive portfolio component featuring:
- **GSAP Sliding-Door Animation**: Smooth expanding doors reveal the portfolio gallery
- **Responsive 3-Column Grid**: Adapts to mobile, tablet, and desktop screens
- **Category Filtering**: Switch between "Web & Hosting" and "AI & Automation" projects
- **Interactive Modal**: Click any project to view detailed information
- **Lazy-Loaded Media**: Optimized images and video support
- **Bilingual Typography**: English and Urdu text support

### 2. **Key Features**
- Premium Slate & Blue color scheme
- GSAP animations for smooth transitions
- Lazy loading for performance optimization
- Responsive design for all devices
- Interactive project modals with tech stack badges
- Live preview links for each project

## Deployment Instructions

### Step 1: Enable GitHub Pages
1. Go to your repository settings
2. Navigate to **Settings → Pages**
3. Under "Source", select **Deploy from a branch**
4. Select **main** branch and **/ (root)** folder
5. Click **Save**

### Step 2: Verify Deployment
- GitHub Pages will automatically build and deploy your site
- Your site will be available at: `https://moon060781-bot.github.io/ng-web-m81`
- Deployment status appears in the "Deployments" section

### Step 3: Custom Domain (Optional)
To use a custom domain:
1. Go to **Settings → Pages**
2. Under "Custom domain", enter your domain (e.g., `noorgee.pk/web`)
3. Add DNS records as instructed by GitHub
4. Enable HTTPS

## File Structure

```
ng-web-m81/
├── index.html              # Main website with Masterpieces component
├── masterpieces.html       # Standalone Masterpieces component
├── MASTERPIECES_GUIDE.md   # Integration documentation
├── _config.yml             # GitHub Pages configuration
├── .nojekyll               # Disables Jekyll processing
├── DEPLOYMENT.md           # This file
├── img/                    # Image assets
├── admin_panel.php         # Admin functionality (local only)
├── send_message.php        # Contact form handler (local only)
└── README.md               # Project README
```

## Features Breakdown

### Masterpieces Component
Located in the portfolio section of `index.html`:

**Initial State (Collapsed)**
- Sleek horizontal bar with "Our Masterpieces" title
- "Explore Our Work" button with arrow icon
- Premium gradient background

**Expanded State**
- Sliding-door animation reveals gallery
- Category tabs for filtering projects
- Responsive 3-column grid of projects
- Each project card shows:
  - Project image/video with hover effects
  - Project title
  - Category badge
  - Click to view detailed modal

**Modal Popup**
- 90% screen size with responsive layout
- Left side: Project media (image or video)
- Right side: Project details including:
  - Title and description
  - Project goals
  - Technology stack (styled badges)
  - Live preview button

## Customization

### Add Your Projects
Edit the `masterpiecesProjects` array in `index.html`:

```javascript
const masterpiecesProjects = [
    {
        id: 'unique-id',
        titleEn: 'Your Project Title',
        categoryEn: 'Web & Hosting', // or 'AI & Automation'
        descriptionEn: 'Short description',
        goalsEn: 'Project goals',
        techStack: ['Tech1', 'Tech2'],
        mediaType: 'image', // or 'video'
        mediaUrl: 'https://example.com/image.jpg',
        liveLink: 'https://example.com',
        category: 'web' // or 'ai'
    }
];
```

### Change Colors
Modify CSS variables in the `<style>` section:

```css
.door-left { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
.explore-button { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
```

### Adjust Animations
Modify GSAP parameters in the JavaScript section:

```javascript
gsap.to(doorLeft, { x: -50, opacity: 0, duration: 0.8, ease: 'power2.inOut' });
```

## Performance Optimization

1. **Image Optimization**
   - Use WebP format where possible
   - Compress images before uploading
   - Use lazy loading (already implemented)

2. **Video Optimization**
   - Use MP4 format for best compatibility
   - Compress videos to reduce file size
   - Consider using video hosting services

3. **Caching**
   - GitHub Pages automatically caches static assets
   - Set appropriate cache headers for optimal performance

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Site Not Deploying
1. Check GitHub Pages settings are enabled
2. Verify branch is set to "main"
3. Check for build errors in Actions tab

### Animations Not Working
1. Ensure GSAP library is loaded: `https://cdnjs.cloudflare.com/ajax/libs/gsap/3.15.0/gsap.min.js`
2. Check browser console for errors
3. Verify JavaScript is enabled

### Images Not Loading
1. Check image URLs are correct
2. Ensure images are publicly accessible
3. Verify image formats are supported (JPG, PNG, WebP, GIF)

### Modal Not Opening
1. Check browser console for errors
2. Verify `portfolio-modal` element exists in HTML
3. Ensure click handlers are properly attached

## Dependencies

- **GSAP 3.15.0**: Animation library (CDN)
- **Lucide Icons**: Icon library (CDN)
- **Tailwind CSS 4**: Styling (CDN)
- **Font Awesome 6**: Icon library (CDN)

All dependencies are loaded via CDN, so no npm installation is required.

## Support & Documentation

- **Integration Guide**: See `MASTERPIECES_GUIDE.md`
- **Component Documentation**: See `README.md`
- **Standalone Version**: Use `masterpieces.html` for standalone implementation

## License

Designed for NoorGee WebMaster and related projects.

---

**Last Updated**: April 2026  
**Version**: 1.0.0
