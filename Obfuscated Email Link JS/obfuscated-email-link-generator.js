/*
$snippet_name = "obfuscated-email-link-generator";
$version = "<!#FV> 0.1.8 </#FV>";


 * Obfuscated Email Link Generator
 * Detects elements with email data attributes and creates functional mailto links
 */


class EmailLinkGenerator {
    constructor() {
        this.defaultTitles = {
            en: "Send email to",
            it: "Invia email a"
        };
        this.defaultCopyTitles = {
            en: "Copy e-mail",
            it: "Copia l'e-mail"
        };
        this.defaultCopyIcon = "far fa-clipboard"; // Default icon for data-copylink="true"
        this.defaultMailtoIcon = "far fa-envelope"; // Default icon for data-icon="true"
        this.defaultLinkWrapperClass = "mk-linkwrapper"; // Default class for the link wrapper
        this.init();
    }

    /**
     * Get the document language from html lang attribute
     * @returns {string} Language code (defaults to 'en')
     */
    getDocumentLanguage() {
        const htmlLang = document.documentElement.lang || document.querySelector('html')?.getAttribute('lang');
        
        if (!htmlLang) {
            return 'en';
        }
        
        // Extract primary language code (e.g., 'en' from 'en-US')
        const primaryLang = htmlLang.toLowerCase().split('-')[0];
        
        // Return the language if we support it, otherwise default to English
        return this.defaultTitles[primaryLang] ? primaryLang : 'en';
    }

    /**
     * Generate default title text based on document language
     * @param {string} email - Full email address
     * @returns {string} Localized title text
     */
    generateDefaultTitle(email) {
        const lang = this.getDocumentLanguage();
        const defaultText = this.defaultTitles[lang];
        return `${defaultText} ${email}`;
    }

    /**
     * Generate default copy title text based on document language
     * @returns {string} Localized copy title text
     */
    generateDefaultCopyTitle() {
        const lang = this.getDocumentLanguage();
        return this.defaultCopyTitles[lang];
    }

    /**
     * Initialize the email link generator
     */
    init() {
        // Run on DOM content loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.processElements());
        } else {
            this.processElements();
        }
    }

    /**
     * Process all elements with email data attributes
     */
    processElements() {
        // Find all elements that have at least data-email and data-domain attributes
        const elements = document.querySelectorAll('[data-email][data-domain]');
        
        elements.forEach(element => {
            try {
                // Only process if not already processed
                if (!element.dataset.emailProcessed) {
                    this.createMailtoLink(element);
                }
            } catch (error) {
                console.warn('Error processing email element:', error, element);
            }
        });
    }

    /**
     * Create and attach mailto link to the specified target element
     * @param {HTMLElement} sourceElement - Element containing the data attributes
     */
    createMailtoLink(sourceElement) {
        const emailData = this.extractEmailData(sourceElement);
        
        if (!this.validateEmailData(emailData)) {
            console.warn('Invalid email data found:', emailData, sourceElement);
            return;
        }

        const mailtoUrl = this.buildMailtoUrl(emailData);
        const targetElement = this.findTargetElement(emailData.className, sourceElement);
        
        if (targetElement) {
            this.attachMailtoLink(targetElement, mailtoUrl, emailData, sourceElement);
        } else {
            console.warn(`Target element with class "${emailData.className}" not found`);
        }
    }

    /**
     * Extract email data from element attributes
     * @param {HTMLElement} element - Source element
     * @returns {Object} Email data object
     */
    extractEmailData(element) {
        return {
            email: element.dataset.email?.trim() || '',
            domain: element.dataset.domain?.trim() || '',
            subject: element.dataset.subject?.trim() || '',
            body: element.dataset.body?.trim() || '',
            className: element.dataset.class?.trim() || '',
            target: element.dataset.target?.trim() || '', // Added target attribute
            title: element.dataset.title?.trim() || '', // Added title attribute
            copyLink: element.dataset.copylink?.trim() || '', // Added copylink attribute
            copyLinkTitle: element.dataset.copylinkTitle?.trim() || '', // Added copylink-title attribute
            copyLinkIcon: element.dataset.copylinkIcon?.trim() || '', // Added copylink-icon attribute
            icon: element.dataset.icon?.trim() || '', // Added icon attribute for mailto link
            linkWrapper: element.dataset.linkwrapper?.trim() || '' // Added linkwrapper attribute
        };
    }

    /**
     * Validate email data
     * @param {Object} emailData - Email data object
     * @returns {boolean} Validation result
     */
    validateEmailData(emailData) {
        // Check required fields
        if (!emailData.email || !emailData.domain) {
            return false;
        }

        // Basic email validation
        const emailRegex = /^[a-zA-Z0-9._-]+$/;
        const domainRegex = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        return emailRegex.test(emailData.email) && domainRegex.test(emailData.domain);
    }

    /**
     * Build complete mailto URL
     * @param {Object} emailData - Email data object
     * @returns {string} Complete mailto URL
     */
    buildMailtoUrl(emailData) {
        const fullEmail = `${emailData.email}@${emailData.domain}`;
        let mailtoUrl = `mailto:${fullEmail}`;

        const params = [];
        
        if (emailData.subject) {
            params.push(`subject=${encodeURIComponent(emailData.subject)}`);
        }
        
        if (emailData.body) {
            params.push(`body=${encodeURIComponent(emailData.body)}`);
        }

        if (params.length > 0) {
            mailtoUrl += `?${params.join('&')}`;
        }

        return mailtoUrl;
    }

    /**
     * Find target element by class name
     * @param {string} className - Target class name
     * @param {HTMLElement} sourceElement - Source element for context
     * @returns {HTMLElement|null} Target element
     */
    findTargetElement(className, sourceElement) {
        if (!className) {
            // If no class specified, use the source element itself
            return sourceElement;
        }

        // First try to find within the same parent container
        const parent = sourceElement.closest('[data-email-container]') || sourceElement.parentElement || document;
        let targetElement = parent.querySelector(`.${className}`);

        // If not found in parent, search globally
        if (!targetElement) {
            targetElement = document.querySelector(`.${className}`);
        }

        return targetElement;
    }

    /**
     * Copy email to clipboard
     * @param {string} email - Email address to copy
     * @returns {Promise<boolean>} Success status
     */
    async copyToClipboard(email) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                // Use modern clipboard API
                await navigator.clipboard.writeText(email);
                return true;
            } else {
                // Fallback for older browsers or non-HTTPS contexts
                const textArea = document.createElement('textarea');
                textArea.value = email;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const result = document.execCommand('copy');
                document.body.removeChild(textArea);
                return result;
            }
        } catch (error) {
            console.warn('Failed to copy email to clipboard:', error);
            return false;
        }
    }

    /**
     * Create copy link button
     * @param {string} fullEmail - Complete email address
     * @param {Object} emailData - Email data object
     * @returns {HTMLElement} Copy link element
     */
    createCopyLinkButton(fullEmail, emailData) {
        const copyButton = document.createElement('a');
        copyButton.href = '#';
        copyButton.className = 'mk-copylink-btn';
        
        let iconClass = emailData.copyLinkIcon;
        let buttonText = emailData.copyLink;

        // If data-copylink is "true", text should be empty and use default icon if none specified
        if (buttonText.toLowerCase() === 'true') {
            buttonText = ''; // Empty text content
            if (!iconClass) {
                iconClass = this.defaultCopyIcon; // Use default icon
            }
        }

        // Add icon if specified
        if (iconClass) {
            const iconElement = document.createElement('i');
            iconElement.className = `mk-copylink-icon ${iconClass}`;
            copyButton.appendChild(iconElement);
        }
        
        // Add text content if not empty
        if (buttonText) {
            const textNode = document.createTextNode(buttonText);
            copyButton.appendChild(textNode);
        }
        
        // Set title - use custom title or generate default
        const copyTitle = emailData.copyLinkTitle || this.generateDefaultCopyTitle();
        copyButton.title = copyTitle;
        
        // Add click event handler
        copyButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const success = await this.copyToClipboard(fullEmail);
            
            if (success) {
                // Optional: Add visual feedback
                const originalTitle = copyButton.title;
                const lang = this.getDocumentLanguage();
                const successText = lang === 'it' ? 'E-mail copiata!' : 'Email copied!';
                copyButton.title = successText;
                
                // Reset title after 2 seconds
                setTimeout(() => {
                    copyButton.title = originalTitle;
                }, 2000);
            }
        });
        
        return copyButton;
    }

    /**
     * Attach mailto link to target element
     * @param {HTMLElement} targetElement - Element to receive the mailto link
     * @param {string} mailtoUrl - Complete mailto URL
     * @param {Object} emailData - Original email data for reference
     * @param {HTMLElement} sourceElement - The original element with data attributes
     */
    attachMailtoLink(targetElement, mailtoUrl, emailData, sourceElement) {
        const fullEmail = `${emailData.email}@${emailData.domain}`;
        const wrapperClassName = emailData.linkWrapper || this.defaultLinkWrapperClass;

        // Create the wrapper div
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = wrapperClassName;
        // Add a flag to the wrapper to easily identify it during refresh
        wrapperDiv.dataset.emailWrapper = 'true';

        // Case 1: targetElement is already an <a> tag
        if (targetElement.tagName.toLowerCase() === 'a') {
            // Update its href and set attributes (including icon)
            targetElement.href = mailtoUrl;
            this.setLinkAttributes(targetElement, emailData, fullEmail);

            const parentNode = targetElement.parentNode;
            if (parentNode) {
                // Replace the existing link with the wrapper div
                parentNode.replaceChild(wrapperDiv, targetElement);
                // Put the existing link inside the wrapper
                wrapperDiv.appendChild(targetElement);
            } else {
                // Fallback if no parent (shouldn't happen in normal DOM)
                console.warn('Target element has no parent, cannot wrap:', targetElement);
                wrapperDiv.appendChild(targetElement);
            }

            // Add copy link if specified, append to the wrapper
            if (emailData.copyLink !== '') {
                const copyButton = this.createCopyLinkButton(fullEmail, emailData);
                wrapperDiv.appendChild(copyButton);
            }

        } else {
            // Case 2: targetElement is a container (div, span, p, etc.)
            // Create new link element
            const link = document.createElement('a');
            link.href = mailtoUrl;

            // Store original innerHTML before clearing targetElement
            const originalInnerHTML = targetElement.innerHTML.trim();

            // Clear target element's content (it will now contain the wrapper)
            targetElement.innerHTML = '';

            // Set attributes and add icon to the NEW link element
            this.setLinkAttributes(link, emailData, fullEmail); // This will prepend icon if needed

            // Now, handle the content of the new link
            // If original content exists, append it to the link (after the icon if any)
            if (originalInnerHTML) {
                link.innerHTML += originalInnerHTML; // Append to existing content (which might be just the icon)
            } else {
                // If no original content and no icon was added, set default email text
                // Check if the link already has content (e.g., from an icon)
                if (link.innerHTML.trim() === '') {
                    link.textContent = fullEmail;
                }
            }
            
            // Append the newly created link to the wrapper
            wrapperDiv.appendChild(link);

            // Add copy link if specified, append to the wrapper
            if (emailData.copyLink !== '') {
                const copyButton = this.createCopyLinkButton(fullEmail, emailData);
                wrapperDiv.appendChild(copyButton);
            }

            // Append the wrapper to the targetElement
            targetElement.appendChild(wrapperDiv);
        }

        // Add processed flag to the original source element to prevent re-processing
        sourceElement.dataset.emailProcessed = 'true';
    }

    /**
     * Set additional attributes for the mailto link
     * @param {HTMLElement} linkElement - Link element
     * @param {Object} emailData - Email data object
     * @param {string} fullEmail - Complete email address
     */
    setLinkAttributes(linkElement, emailData, fullEmail) {
        // Set title attribute - use custom title or generate default
        let titleText = emailData.title || this.generateDefaultTitle(fullEmail);
        
        // Add class for styling if not already present
        if (!linkElement.classList.contains('mk-mailto-link')) { // Renamed class
            linkElement.classList.add('mk-mailto-link'); // Renamed class
        }

        // Store original email data as data attributes for reference
        linkElement.dataset.originalEmail = fullEmail;

        // Handle target attribute - only add target="_blank" if data-target is present
        if (emailData.target !== '') {
            linkElement.target = '_blank';
            // Add rel="noopener noreferrer" for security when opening in new tab
            linkElement.rel = 'noopener noreferrer';
            
            // Update title to indicate it opens in new tab
            const lang = this.getDocumentLanguage();
            const newTabText = lang === 'it' ? ' (Si apre in una nuova scheda)' : ' (Opens in new tab)';
            titleText += newTabText;
        }

        // Set the final title attribute
        linkElement.title = titleText;

        // Add icon to the link if specified (for existing <a> tags or newly created ones)
        let mailtoIconClass = emailData.icon;
        if (mailtoIconClass.toLowerCase() === 'true') {
            mailtoIconClass = this.defaultMailtoIcon;
        }
        // IMPORTANT: Only add the icon if it's specified AND it's not already present
        if (mailtoIconClass && !linkElement.querySelector('.mk-icon')) { 
            const iconElement = document.createElement('i');
            iconElement.className = `mk-icon ${mailtoIconClass}`;
            linkElement.prepend(iconElement); // Prepend the icon to the link's content
        }
    }

    /**
     * Manually process a specific element (useful for dynamic content)
     * @param {HTMLElement} element - Element to process
     */
    processElement(element) {
        if (element.dataset.email && element.dataset.domain && !element.dataset.emailProcessed) {
            this.createMailtoLink(element);
        }
    }

    /**
     * Refresh all email links (useful after DOM updates)
     */
    refresh() {
        // Remove processed flags from source elements
        document.querySelectorAll('[data-email-processed]').forEach(el => {
            delete el.dataset.emailProcessed;
        });
        
        // Remove existing wrapper divs (which contain the links and copy buttons)
        // We target the default wrapper class and any wrapper with the data-email-wrapper flag
        document.querySelectorAll(`.${this.defaultLinkWrapperClass}, [data-email-wrapper="true"]`).forEach(wrapper => {
            wrapper.remove();
        });
        
        // Reprocess all elements
        this.processElements();
    }
}

// Auto-initialize when script loads
const emailLinkGenerator = new EmailLinkGenerator();

// Expose global methods for manual control
window.EmailLinkGenerator = {
    processElement: (element) => emailLinkGenerator.processElement(element),
    refresh: () => emailLinkGenerator.refresh(),
    instance: emailLinkGenerator
};
