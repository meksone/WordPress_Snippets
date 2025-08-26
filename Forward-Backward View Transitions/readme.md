# CSS View Transitions Navigation Direction Detection

**Version:** 0.0.5  
**Status:** Working version for forward and backwards animations

## Overview

This script detects navigation direction (forward/backward) and applies different CSS View Transition animations accordingly, creating smooth page transitions that respond appropriately to user navigation patterns.

## How It Works

### Animation Behavior

- **FORWARD navigation** (links, form submissions):  
  Old page slides UP, new page slides in from BOTTOM

- **BACKWARD navigation** (back button, browser back):  
  Old page slides DOWN, new page slides in from TOP

### Detection Methods

The script uses two complementary detection methods to ensure compatibility across different browsers:

#### 1. Performance API
Uses `performance.getEntriesByType('navigation')[0].type` to detect if the current page was loaded via `back_forward` navigation. This method works immediately when the page loads.

#### 2. Navigation API (Modern Browsers)
For browsers that support the Navigation API, the script listens for navigation events and stores the direction in `sessionStorage` for the next page to read. This provides more accurate detection for supported browsers.

### Animation Control

The script controls animations using a CSS custom property:

- **`--wipe-direction: 1`** = Forward animation
- **`--wipe-direction: -1`** = Backward animation (reversed)

This approach allows for smooth mathematical transitions using `calc()` functions in CSS keyframes.

## Browser Compatibility

- **CSS View Transitions:** Chrome/Edge/Opera/Safari/Android (check [caniuse.com/view-transitions](https://caniuse.com/view-transitions))
- **Navigation API:** Modern browsers with fallback to Performance API
- **Graceful degradation:** Works on older browsers with reduced functionality

## Configuration

### Debug Mode

Set the debug configuration at the top of the script:

```javascript
const DEBUG_ENABLED = true;  // Set to false to disable console logging
```

- true: Shows detailed console logs for development/debugging
- false: Clean console output for production

### Debug Function
Run debugNavigation() in the browser console to see current state and navigation details:

```Javascript
debugNavigation()
```

Output includes:

- Current animation direction value
- Navigation entry type
- Stored direction in sessionStorage
- Current URL
- Animation interpretation (FORWARD/BACKWARD)

## Usage instructions

### Implementation

CSS Structure

```Css
@view-transition { 
    navigation: auto;
}

:root {
    --wipe-direction: 1; /* Controlled by JavaScript */
}

::view-transition-old(root) {
    animation: wipe-out 400ms ease-in-out;
}

::view-transition-new(root) {
    animation: wipe-in 400ms ease-in-out;
}
```

### JavaScript Detection

- Immediate detection on page load using Performance API
- Future navigation setup using Navigation API + sessionStorage
- Fallback compatibility for browsers without Navigation API


## Usage Instructions

- Add the complete script to your website's <head> section
- Ensure CSS View Transitions are supported by the target browsers
- Test navigation in both directions (forward links and back button)
- Use debugNavigation() to troubleshoot if needed
- Set DEBUG_ENABLED = false for production deployment


## Technical Notes

- The script runs immediately when loaded to catch navigation type
- Uses sessionStorage to communicate between page loads
- CSS custom properties provide dynamic animation control
- Graceful fallback ensures functionality across browser versions
- No external dependencies required