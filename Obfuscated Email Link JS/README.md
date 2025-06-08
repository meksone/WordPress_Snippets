# 📧Obfuscated Email Link Generator JS

This script was primarily created for use in WordPress websites, using **Elementor Pro** or other page builders that make it easy to add "data-attributes" to elements;

However, it can be used on any type of website, on plain HTML or Javascript, as noted in the examples provided.

## ⭐Key features

- **Automatic Detection** - Automatically finds and processes elements with email data attributes
- **Flexible Targeting** - Can target specific elements by class or use the source element
- **URL Encoding** - Properly encodes subject and body parameters
- **Validation** - Validates email and domain formats
- **Error Handling** - Graceful error handling with console warnings
- **Accessibility** - Adds title attributes for better accessibility
- **Dynamic Content Support** - Methods for processing dynamically added content
- **Performance Optimized** - Avoids duplicate processing with flags

The script will automatically run when the DOM is loaded and create functional mailto links based on your data attributes.

### Data attributes used for this script to work:

| Data attribute | Required? | Description |
|---|---|---|
|**data-email**  | **Yes**   | **the username part of the email (all the text before the @)**
|**data-domain** | **Yes**   | **only the domain part (after the @, and without @ itself)**
|data-subject    | No        | the subject of the email 
|data-body       | No        | the body of the email
|data-class      | No        | the class of the element that must be converted to email link
|data-target     | No        | specify if the link should open in a new tab; any value is valid (true,yes, ciao,⭐)<br>because only the presence is really checked


## 🔐Security Notes

When adding target="_blank", the script automatically includes rel="noopener noreferrer" for security reasons. This prevents the new page from accessing the window.opener property and protects against potential security vulnerabilities.


## 🛠️To do and corrections

Whilst it's a completely functional script, there's still rooms for improvement; 
I want to  point out some little details that I want to correct in the near future:

- in the **setLinkAttributes** function, the link has a hover message that is hardcoded
(linkElement.title = `Send email to ${emailData.email}@${emailData.domain}`;); i plan to make this configurable using another data-attribute
- also in the same function, there's hardcoded class called **mailto-link** that I want to make configurable through another data-attribute, different from data-class that has a different purpose

### ℹ️Disclaimer

Please consider I'm not a professional developer; I'm an **IT professional for over two decades** who loves to create websites using **WordPress** and I have basic knownledge of programming languages and techniques and related security practices. If you plan to use this script please consider to personally review the code and do the necessary changes to accommodate to your case.

⚠️**The script is offered "as-is" without any guarantee, use it at your own risk (sorry, I must say this, there's alot of bad people out there! 😅)**

### 🤖AI Disclaimer

As previously stated, I'm not a professional developer and I've used AI (Claude 4 Sonnet) to create and test this script; though, I've personally tested on my environment and I'm feeling confident to use in public exposed sites. Feel free to review, modify, fork, suggest, ignore it or whatever else you want 

## 💾Implement in your site

In WordPress, you can just use a simple plugin like [Code Snippets](https://codesnippets.pro/) and simply copy the JS code as a functional snippet.
If you prefer, you can output the JS code using simple PHP in Code Snippets, like this

```php
/* Javascript starts */
?>
    <script type="text/javascript">

    ... paste the code here ...

    </script>
<?php
/* Javascript ends */
```
If you use **Elementor Pro**, you can also use the Custom Code section and paste the javascript directly here, enclosed in script tags
```html
<script>
    ... paste code here ...
</script>
``` 
this method has the advantage of using conditional loading from Elementor, and you can load it where you want (IMHO, it's better to load it everywhere but it's up to you) 

If you prefer, you can also save the javascript in a .js file, put it where you want in you theme path (better in child theme) and the call it using 

```html
<script src="path/to/your/file.js" type="text/javascript"></script>
```


## ⭐Usage Examples

### HTML
```html
<!DOCTYPE html>
<html>
<head>
    <title>Email Link Generator Example</title>
    <style>
        .mailto-link {
            color: #0066cc;
            text-decoration: underline;
        }
        .email-button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Example 1: Email link that opens in new tab -->
    <div data-email="john.doe" 
         data-domain="example.com" 
         data-class="email-link"
         data-target="_blank">
        <span class="email-link">Contact John (Opens in new tab)</span>
    </div>

    <!-- Example 2: Email link that opens in same tab (no data-target) -->
    <div data-email="support" 
         data-domain="company.com" 
         data-subject="Help Request" 
         data-body="Hi, I need help with..."
         data-class="support-link">
        <a class="support-link">Get Support (Same tab)</a>
    </div>

    <!-- Example 3: Email button with new tab -->
    <div data-email="sales" 
         data-domain="business.com" 
         data-subject="Sales Inquiry" 
         data-class="email-button"
         data-target="">
        <button class="email-button">Contact Sales (New tab)</button>
    </div>

    <!-- Example 4: Email with all parameters including new tab -->
    <div data-email="info" 
         data-domain="website.com"
         data-subject="Information Request"
         data-body="Hello, I would like more information about..."
         data-class="info-link"
         data-target="_blank">
        <a class="info-link">Get More Info</a>
    </div>

    <!-- Example 5: No target attribute - opens in same tab -->
    <span data-email="contact" 
          data-domain="example.org"
          data-class="contact-link">
        <span class="contact-link">Contact Us</span>
    </span>

    <script src="email-link-generator.js"></script>
</body>
</html>
```

### Javascript

```javascript
// Create element that opens email in new tab
const newTabElement = document.createElement('div');
newTabElement.dataset.email = 'newtab';
newTabElement.dataset.domain = 'example.com';
newTabElement.dataset.class = 'new-tab-link';
newTabElement.dataset.target = '_blank'; // This will make it open in new tab
newTabElement.innerHTML = '<span class="new-tab-link">New Tab Email</span>';
document.body.appendChild(newTabElement);

// Create element that opens email in same tab
const sameTabElement = document.createElement('div');
sameTabElement.dataset.email = 'sametab';
sameTabElement.dataset.domain = 'example.com';
sameTabElement.dataset.class = 'same-tab-link';
// No data-target attribute - will open in same tab
sameTabElement.innerHTML = '<span class="same-tab-link">Same Tab Email</span>';
document.body.appendChild(sameTabElement);

// Process the new elements
EmailLinkGenerator.processElement(newTabElement);
EmailLinkGenerator.processElement(sameTabElement);
```

