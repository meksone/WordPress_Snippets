/*
$snippet_name = "obfuscated-email-link-generator";
$version = "<!#FV> 0.1.1 </#FV>";


 * Obfuscated Email Link Generator
 * Detects elements with email data attributes and creates functional mailto links
 */


class EmailLinkGenerator {
    constructor() {
        this.init();
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
                this.createMailtoLink(element);
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
            this.attachMailtoLink(targetElement, mailtoUrl, emailData);
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
            target: element.dataset.target?.trim() || '' // Added target attribute
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
     * Attach mailto link to target element
     * @param {HTMLElement} targetElement - Element to receive the mailto link
     * @param {string} mailtoUrl - Complete mailto URL
     * @param {Object} emailData - Original email data for reference
     */
    attachMailtoLink(targetElement, mailtoUrl, emailData) {
        // If target is already a link, just update href
        if (targetElement.tagName.toLowerCase() === 'a') {
            targetElement.href = mailtoUrl;
            this.setLinkAttributes(targetElement, emailData);
        } else {
            // Create new link element
            const link = document.createElement('a');
            link.href = mailtoUrl;
            this.setLinkAttributes(link, emailData);

            // If target element has content, wrap it
            if (targetElement.innerHTML.trim()) {
                link.innerHTML = targetElement.innerHTML;
                targetElement.innerHTML = '';
                targetElement.appendChild(link);
            } else {
                // If target is empty, set default text
                link.textContent = `${emailData.email}@${emailData.domain}`;
                targetElement.appendChild(link);
            }
        }

        // Add processed flag to avoid duplicate processing
        targetElement.dataset.emailProcessed = 'true';
    }

    /**
     * Set additional attributes for the mailto link
     * @param {HTMLElement} linkElement - Link element
     * @param {Object} emailData - Email data object
     */
    setLinkAttributes(linkElement, emailData) {
        // Add title attribute for accessibility
        linkElement.title = `Send email to ${emailData.email}@${emailData.domain}`;
        
        // Add class for styling if not already present
        if (!linkElement.classList.contains('mailto-link')) {
            linkElement.classList.add('mailto-link');
        }

        // Store original email data as data attributes for reference
        linkElement.dataset.originalEmail = `${emailData.email}@${emailData.domain}`;

        // Handle target attribute - only add target="_blank" if data-target is present
        if (emailData.target !== '') {
            linkElement.target = '_blank';
            // Add rel="noopener noreferrer" for security when opening in new tab
            linkElement.rel = 'noopener noreferrer';
            
            // Update title to indicate it opens in new tab
            linkElement.title += ' (Opens in new tab)';
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
        // Remove processed flags
        document.querySelectorAll('[data-email-processed]').forEach(el => {
            delete el.dataset.emailProcessed;
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