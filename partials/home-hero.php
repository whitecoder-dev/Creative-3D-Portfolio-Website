<section class="section" id="home" data-reveal>
    <div class="glass" style="padding: clamp(1.2rem, 3.5vw, 2rem);">
        <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> Premium Portfolio</span>
        <h1 style="max-width: 16ch; margin-top: 0.65rem;">
            <?php echo e((string) config('site.hero_tagline')); ?>
        </h1>
        <p style="margin-top: 0.9rem; max-width: 60ch; font-size: 1.03rem;">
            Founder of WhiteCoder and HND Study Hub. I design clean experiences and production-grade systems across modern web and AI.
        </p>
        <div class="btn-row" style="margin-top: 1.15rem;">
            <a class="btn primary" href="<?php echo e(site_url('works.php')); ?>"><i class="fa-solid fa-briefcase"></i> Explore Works</a>
            <a class="btn ghost" href="<?php echo e(site_url('contact.php')); ?>"><i class="fa-regular fa-paper-plane"></i> Start a Project</a>
        </div>

        <div class="grid cols-3" style="margin-top: 1.3rem;">
            <article class="card" style="padding: 0.9rem;">
                <span class="pill"><i class="fa-solid fa-diagram-project"></i> Projects</span>
                <h3 style="margin-top: 0.65rem;">50+ Projects Built</h3>
                <p>From student products to production-ready client systems.</p>
            </article>
            <article class="card" style="padding: 0.9rem;">
                <span class="pill"><i class="fa-solid fa-users"></i> Impact</span>
                <h3 style="margin-top: 0.65rem;">6K+ Users / Students</h3>
                <p>Educational and technical systems used by active communities.</p>
            </article>
            <article class="card" style="padding: 0.9rem;">
                <span class="pill"><i class="fa-regular fa-clock"></i> Experience</span>
                <h3 style="margin-top: 0.65rem;">3+ Years Building</h3>
                <p>Focused on scalable web engineering and AI integration.</p>
            </article>
        </div>
    </div>
</section>
