<?php
/**
 * Footer Template
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Section -->
            <div class="footer-section">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <?php $logo = mallfinder_get_logo(); ?>
                    <?php if ($logo) : ?>
                        <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(mallfinder_get_site_name()); ?>" style="max-height: 48px; max-width: 48px;" />
                    <?php else : ?>
                        <div style="background: var(--gradient-primary); padding: 0.5rem; border-radius: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/></svg>
                        </div>
                    <?php endif; ?>
                    <span style="font-weight: 700; font-size: 1.125rem; background: linear-gradient(to right, #34d399, #14b8a6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo esc_html(mallfinder_get_site_name()); ?></span>
                </a>
                <p style="color: var(--color-slate-400); font-size: 0.875rem; margin-bottom: 1rem;">
                    <?php echo esc_html(mallfinder_get_site_tagline()); ?>
                </p>
                <?php mallfinder_social_icons_footer(); ?>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-section">
                <h4><?php _e('Quick Links', 'mallfinder'); ?></h4>
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/about/')); ?>"><?php _e('About Us', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php _e('Contact Us', 'mallfinder'); ?></a>
            </div>
            
            <!-- Categories -->
            <div class="footer-section">
                <h4><?php _e('Categories', 'mallfinder'); ?></h4>
                <a href="<?php echo esc_url(add_query_arg('status', 'existing', home_url('/'))); ?>"><?php _e('Existing Malls', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(add_query_arg('status', 'upcoming', home_url('/'))); ?>"><?php _e('Upcoming Malls', 'mallfinder'); ?></a>
            </div>
            
            <!-- Contact -->
            <div class="footer-section">
                <h4><?php _e('Contact', 'mallfinder'); ?></h4>
                <?php $whatsapp = get_option('mallfinder_whatsapp_number'); ?>
                <?php if ($whatsapp) : ?>
                    <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?php _e('Chat with us', 'mallfinder'); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" style="color: var(--color-emerald-400);"><?php _e('Send us a message', 'mallfinder'); ?> →</a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container" style="display: flex; flex-direction: column; gap: 1rem;">
            <p>© <?php echo date('Y'); ?> <?php echo esc_html(mallfinder_get_site_name()); ?>. <?php _e('All rights reserved.', 'mallfinder'); ?></p>
            <div class="footer-bottom-links">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/about/')); ?>"><?php _e('About', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php _e('Contact', 'mallfinder'); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php mallfinder_go_to_top(); ?>

<?php wp_footer(); ?>

<script>
// Mobile Menu Toggle
document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
    document.getElementById('mobile-nav').classList.toggle('active');
});

// Go to Top Button
window.addEventListener('scroll', function() {
    var goTop = document.getElementById('go-to-top');
    if (window.scrollY > 300) {
        goTop.classList.add('visible');
    } else {
        goTop.classList.remove('visible');
    }
});

document.getElementById('go-to-top').addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>

</body>
</html>
