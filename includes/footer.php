<?php
declare(strict_types=1);
?>
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div>
            <strong><?php echo e((string) config('site.name')); ?></strong><br>
            <span><?php echo e((string) config('site.brand_title')); ?></span><br>
            <span>&copy; <span id="yearNow"></span> All rights reserved.</span>
        </div>

        <div>
            <div class="social-links">
                <a href="<?php echo e((string) config('social.github')); ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                <a href="<?php echo e((string) config('social.linkedin')); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="<?php echo e((string) config('social.instagram')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="mailto:<?php echo e((string) config('contact.email')); ?>" aria-label="Email"><i class="fa-regular fa-envelope"></i></a>
            </div>
        </div>
    </div>
</footer>

<div class="toasts" id="toastRoot" aria-live="polite" aria-atomic="true"></div>

<script>
(() => {
    const menuButton = document.querySelector('[data-menu-toggle]');
    const nav = document.getElementById('siteNav');

    if (menuButton && nav) {
        menuButton.addEventListener('click', () => {
            nav.classList.toggle('open');
        });
    }

    document.getElementById('yearNow').textContent = String(new Date().getFullYear());

    const progressBar = document.getElementById('scrollProgress');
    const updateProgress = () => {
        const scrollTop = window.scrollY;
        const height = document.documentElement.scrollHeight - window.innerHeight;
        const progress = height <= 0 ? 0 : (scrollTop / height) * 100;
        progressBar.style.width = `${Math.max(0, Math.min(100, progress))}%`;
    };
    updateProgress();
    window.addEventListener('scroll', updateProgress, { passive: true });

    const revealTargets = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        revealTargets.forEach((el) => observer.observe(el));
    } else {
        revealTargets.forEach((el) => el.classList.add('active'));
    }

    window.showToast = (message, type = 'success') => {
        const root = document.getElementById('toastRoot');
        if (!root) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        root.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            toast.style.transition = 'all 0.26s ease';
            setTimeout(() => toast.remove(), 260);
        }, 2600);
    };
})();
</script>

<script type="module">
(async () => {
    const holder = document.getElementById('three-bg');
    const body = document.body;

    if (!holder || !body) {
        return;
    }

    const modelPath = body.dataset.model || '';
    const baseUrl = (body.dataset.baseUrl || '').replace(/\/$/, '');

    if (!modelPath) {
        return;
    }

    const supportsWebGl = (() => {
        try {
            const canvas = document.createElement('canvas');
            return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
        } catch (error) {
            return false;
        }
    })();

    if (!supportsWebGl) {
        holder.style.display = 'none';
        return;
    }

    let THREE;
    let GLTFLoader;

    try {
        THREE = await import('https://unpkg.com/three@0.165.0/build/three.module.js');
        ({ GLTFLoader } = await import('https://unpkg.com/three@0.165.0/examples/jsm/loaders/GLTFLoader.js'));
    } catch (error) {
        holder.style.display = 'none';
        return;
    }

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(36, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(0, 0, 7);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);
    holder.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 1.1));
    const directional = new THREE.DirectionalLight(0x87b9ff, 1.3);
    directional.position.set(2.2, 2.4, 3.5);
    scene.add(directional);

    const loader = new GLTFLoader();
    const modelUrl = modelPath.startsWith('http')
        ? modelPath
        : `${baseUrl}${modelPath.startsWith('/') ? '' : '/'}${modelPath}`;

    let model = null;

    loader.load(
        modelUrl,
        (gltf) => {
            model = gltf.scene;
            model.scale.set(1.8, 1.8, 1.8);
            model.position.set(2.4, -0.6, 0);
            scene.add(model);
        },
        undefined,
        () => {
            holder.style.display = 'none';
        }
    );

    const pointer = { x: 0, y: 0 };

    window.addEventListener('mousemove', (event) => {
        pointer.x = (event.clientX / window.innerWidth) * 2 - 1;
        pointer.y = (event.clientY / window.innerHeight) * 2 - 1;
    }, { passive: true });

    const onResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    };
    window.addEventListener('resize', onResize);

    const tick = () => {
        if (model) {
            model.rotation.y += 0.0018;
            model.rotation.x = 0.08 + pointer.y * 0.04;
            model.position.x = 2.3 + pointer.x * 0.2;
        }
        renderer.render(scene, camera);
        requestAnimationFrame(tick);
    };

    tick();
})();
</script>

</body>
</html>
