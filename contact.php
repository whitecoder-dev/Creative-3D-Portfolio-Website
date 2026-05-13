<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'contact';
$pageModel = '/models/contact.glb';
$seo = [
    'title' => 'Contact | Oshanda Geethanjana',
    'description' => 'Contact Oshanda Geethanjana for web development and AI engineering collaborations.',
    'keywords' => 'Contact Oshanda Geethanjana, web project inquiry, AI consultation, WhiteCoder contact',
    'slug' => 'contact',
    'canonical' => site_url('contact.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'Contact' => site_url('contact.php'),
    ],
];

$csrf = csrf_token();

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="glass" style="padding: 1.25rem;">
        <span class="eyebrow"><i class="fa-regular fa-envelope"></i> Contact</span>
        <h1 style="margin-top: 0.6rem;">Let us Build Something Meaningful</h1>
        <p style="margin-top: 0.75rem; max-width: 64ch;">
            Share your idea, challenge, or collaboration plan. I typically respond within 24-48 hours.
        </p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="grid cols-2" style="align-items: start;">
        <article class="card">
            <h2>Send a Message</h2>
            <form id="contactForm" style="margin-top: 1rem; display:grid; gap:0.72rem;">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
                <input type="text" name="company_website" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px; opacity:0; pointer-events:none;" aria-hidden="true">

                <div>
                    <label for="name" style="font-weight:700; font-size:0.86rem;">Name</label>
                    <input id="name" class="input" type="text" name="name" maxlength="150" required>
                </div>
                <div>
                    <label for="email" style="font-weight:700; font-size:0.86rem;">Email</label>
                    <input id="email" class="input" type="email" name="email" maxlength="200" required>
                </div>
                <div>
                    <label for="subject" style="font-weight:700; font-size:0.86rem;">Subject</label>
                    <input id="subject" class="input" type="text" name="subject" maxlength="255" required>
                </div>
                <div>
                    <label for="project_type" style="font-weight:700; font-size:0.86rem;">Project Type</label>
                    <select id="project_type" class="select" name="project_type">
                        <option value="">Select project type</option>
                        <option value="Portfolio Website">Portfolio Website</option>
                        <option value="Business Web App">Business Web App</option>
                        <option value="AI Integration">AI Integration</option>
                        <option value="Learning Platform">Learning Platform</option>
                        <option value="Consultation">Consultation</option>
                    </select>
                </div>
                <div>
                    <label for="message" style="font-weight:700; font-size:0.86rem;">Message</label>
                    <textarea id="message" class="textarea" name="message" maxlength="5000" required></textarea>
                </div>

                <div class="btn-row" style="margin-top: 0.2rem;">
                    <button type="submit" class="btn primary" id="contactSubmit"><i class="fa-regular fa-paper-plane"></i> Send Message</button>
                    <button type="reset" class="btn subtle">Reset</button>
                </div>
            </form>
        </article>

        <aside class="grid" style="gap:0.8rem;">
            <article class="card">
                <span class="pill"><i class="fa-solid fa-circle-check"></i> Availability</span>
                <p style="margin-top: 0.6rem;"><?php echo e((string) config('site.availability')); ?></p>
            </article>
            <article class="card">
                <h3>Contact Info</h3>
                <div style="margin-top: 0.7rem; display:grid; gap:0.48rem;">
                    <p><i class="fa-regular fa-envelope"></i> <a href="mailto:<?php echo e((string) config('contact.email')); ?>"><?php echo e((string) config('contact.email')); ?></a></p>
                    <p><i class="fa-brands fa-whatsapp"></i> <a href="https://wa.me/<?php echo e(preg_replace('/\D+/', '', (string) config('contact.whatsapp'))); ?>" target="_blank" rel="noopener noreferrer"><?php echo e((string) config('contact.whatsapp')); ?></a></p>
                    <p><i class="fa-brands fa-github"></i> <a href="<?php echo e((string) config('social.github')); ?>" target="_blank" rel="noopener noreferrer">github.com/oshandageethanjana</a></p>
                    <p><i class="fa-brands fa-linkedin"></i> <a href="<?php echo e((string) config('social.linkedin')); ?>" target="_blank" rel="noopener noreferrer">linkedin.com/in/oshanda-geethanjana</a></p>
                    <p><i class="fa-brands fa-instagram"></i> <a href="<?php echo e((string) config('social.instagram')); ?>" target="_blank" rel="noopener noreferrer">@whitecoder._</a></p>
                </div>
            </article>

            <article class="card">
                <h3>Location</h3>
                <p style="margin-top: 0.55rem;">Colombo, Sri Lanka</p>
                <div style="margin-top: 0.7rem; border-radius: 14px; height: 160px; background: linear-gradient(140deg, #edf4ff, #f9fcff); border:1px solid rgba(19,48,84,0.12); display:grid; place-items:center; color:#4e6787; font-weight:700;">
                    Google Map Placeholder - Colombo
                </div>
            </article>
        </aside>
    </div>
</section>

<script>
(() => {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('contactSubmit');
    const baseUrl = document.body.dataset.baseUrl || '';

    const validate = (formData) => {
        const name = (formData.get('name') || '').trim();
        const email = (formData.get('email') || '').trim();
        const subject = (formData.get('subject') || '').trim();
        const message = (formData.get('message') || '').trim();

        if (name.length < 2) {
            return 'Please enter a valid name.';
        }

        if (!/^\S+@\S+\.\S+$/.test(email)) {
            return 'Please enter a valid email address.';
        }

        if (subject.length < 3) {
            return 'Please provide a clear subject.';
        }

        if (message.length < 10) {
            return 'Please write a more detailed message.';
        }

        return '';
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const validationError = validate(formData);

        if (validationError) {
            showToast(validationError, 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

        try {
            const response = await fetch(`${baseUrl}/api/submit_contact.php`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to submit your message right now.');
            }

            form.reset();
            showToast(payload.message || 'Message sent successfully.', 'success');
        } catch (error) {
            showToast(error.message || 'Something went wrong. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-regular fa-paper-plane"></i> Send Message';
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
