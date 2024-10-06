const puppeteer = require('puppeteer');

function getLuminance(r, g, b) {
    const a = [r, g, b].map(v => {
        v /= 255;
        return v <= 0.03928
            ? v / 12.92
            : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

function getContrastRatio(rgb1, rgb2) {
    const lum1 = getLuminance(rgb1[0], rgb1[1], rgb1[2]);
    const lum2 = getLuminance(rgb2[0], rgb2[1], rgb2[2]);
    const brightest = Math.max(lum1, lum2);
    const darkest = Math.min(lum1, lum2);
    return (brightest + 0.05) / (darkest + 0.05);
}

async function analyzeColorContrast(url) {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto(url, { waitUntil: 'networkidle0' });

    const contrastIssues = await page.evaluate(() => {
        const elements = document.body.getElementsByTagName('*');
        const issues = [];

        function parseRGB(color) {
            const match = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            return match ? [parseInt(match[1]), parseInt(match[2]), parseInt(match[3])] : null;
        }

        for (let element of elements) {
            const style = window.getComputedStyle(element);
            const backgroundColor = parseRGB(style.backgroundColor);
            const color = parseRGB(style.color);

            if (backgroundColor && color) {
                const contrastRatio = getContrastRatio(backgroundColor, color);
                if (contrastRatio < 4.5) {  // WCAG AA standard for normal text
                    issues.push({
                        element: element.tagName,
                        backgroundColor: style.backgroundColor,
                        color: style.color,
                        contrastRatio: contrastRatio.toFixed(2)
                    });
                }
            }
        }

        return issues;
    });

    await browser.close();

    return {
        url,
        contrastIssues,
        totalIssues: contrastIssues.length
    };
}

// If this script is run directly (not imported as a module)
if (require.main === module) {
    const url = process.argv[2];
    if (!url) {
        console.error('Please provide a URL as an argument');
        process.exit(1);
    }

    analyzeColorContrast(url)
        .then(result => console.log(JSON.stringify(result, null, 2)))
        .catch(error => console.error('Error:', error));
}

module.exports = analyzeColorContrast;
