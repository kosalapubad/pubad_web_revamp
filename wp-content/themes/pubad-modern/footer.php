<?php
/**
 * Footer template.
 *
 * @package PubadModern
 */
?>
</main>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-brand">
			<img src="<?php echo pubad_modern_asset( 'government-logo.png' ); ?>" alt="<?php esc_attr_e( 'Government of Sri Lanka Logo', 'pubad-modern' ); ?>">
			<h2><?php esc_html_e( 'Ministry of', 'pubad-modern' ); ?><br><?php esc_html_e( 'Public Administration', 'pubad-modern' ); ?></h2>
			<p><?php esc_html_e( 'Sri Lanka', 'pubad-modern' ); ?></p>
			<p><?php esc_html_e( 'Committed to building an efficient, effective and people-friendly public service for the nation.', 'pubad-modern' ); ?></p>
			<div class="socials"><a href="#">f</a><a href="#">X</a><a href="#">▶</a><a href="#">in</a></div>
		</div>
		<div>
			<h3><?php esc_html_e( 'Important Links', 'pubad-modern' ); ?></h3>
			<?php wp_nav_menu( array( 'theme_location' => 'footer_1', 'container' => false, 'fallback_cb' => 'pubad_modern_footer_one' ) ); ?>
		</div>
		<div>
			<h3><?php esc_html_e( 'Other Links', 'pubad-modern' ); ?></h3>
			<?php wp_nav_menu( array( 'theme_location' => 'footer_2', 'container' => false, 'fallback_cb' => 'pubad_modern_footer_two' ) ); ?>
		</div>
		<div class="footer-contact">
			<h3><?php esc_html_e( 'Contact Us', 'pubad-modern' ); ?></h3>
			<p><?php echo pubad_modern_icon( 'pin' ); ?><?php esc_html_e( 'Independence Square, Colombo 07, Sri Lanka', 'pubad-modern' ); ?></p>
			<p><?php echo pubad_modern_icon( 'phone' ); ?>+94 112 682 162</p>
			<p><?php echo pubad_modern_icon( 'mail' ); ?>info@pubad.gov.lk</p>
			<p><?php echo pubad_modern_icon( 'info' ); ?><?php esc_html_e( 'Mon - Fri 8.30 AM - 4.30 PM', 'pubad-modern' ); ?></p>
		</div>
	</div>
	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Ministry of Public Administration, Provincial Councils and Local Government. All Rights Reserved.', 'pubad-modern' ); ?></p>
		<p><a href="#"><?php esc_html_e( 'Privacy Policy', 'pubad-modern' ); ?></a><a href="#"><?php esc_html_e( 'Terms of Use', 'pubad-modern' ); ?></a></p>
	</div>
	<div class="ai-chat" data-ai-chat>
		<section class="ai-chat__panel" aria-label="<?php esc_attr_e( 'AI Assistant chat', 'pubad-modern' ); ?>" hidden>
			<header class="ai-chat__header">
				<div>
					<strong><?php esc_html_e( 'AI Assistant', 'pubad-modern' ); ?></strong>
					<span><?php esc_html_e( 'Ministry help desk', 'pubad-modern' ); ?></span>
				</div>
				<button class="ai-chat__close" type="button" aria-label="<?php esc_attr_e( 'Close chat', 'pubad-modern' ); ?>">&times;</button>
			</header>
			<div class="ai-chat__messages" role="log" aria-live="polite">
				<div class="ai-message ai-message--bot">
					<?php esc_html_e( 'Hello. I can help you find contact details, office hours, circulars, notices, divisions, services, downloads, and bungalow booking information.', 'pubad-modern' ); ?>
				</div>
			</div>
			<div class="ai-chat__suggestions" aria-label="<?php esc_attr_e( 'Suggested questions', 'pubad-modern' ); ?>">
				<button type="button"><?php esc_html_e( 'Contact details', 'pubad-modern' ); ?></button>
				<button type="button"><?php esc_html_e( 'Office hours', 'pubad-modern' ); ?></button>
				<button type="button"><?php esc_html_e( 'Bungalow booking', 'pubad-modern' ); ?></button>
			</div>
			<form class="ai-chat__form">
				<label class="screen-reader-text" for="ai-chat-input"><?php esc_html_e( 'Ask the assistant', 'pubad-modern' ); ?></label>
				<input id="ai-chat-input" type="text" name="message" autocomplete="off" placeholder="<?php esc_attr_e( 'Type your question...', 'pubad-modern' ); ?>">
				<button type="submit"><?php esc_html_e( 'Send', 'pubad-modern' ); ?></button>
			</form>
		</section>
		<button class="ai-assistant" type="button" aria-expanded="false">
			<span><?php esc_html_e( 'AI Assistant', 'pubad-modern' ); ?><small><?php esc_html_e( 'How can I help you?', 'pubad-modern' ); ?></small></span>
			<b>AI</b>
		</button>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
<?php
function pubad_modern_footer_one() {
	echo '<ul><li><a href="#">Home</a></li><li><a href="#">About Us</a></li><li><a href="#">Services</a></li><li><a href="#">Circulars</a></li><li><a href="#">Notices</a></li><li><a href="#">Contact Us</a></li></ul>';
}

function pubad_modern_footer_two() {
	echo '<ul><li><a href="#">Sitemap</a></li><li><a href="#">Webmail</a></li><li><a href="#">Employee Login</a></li><li><a href="#">Grievance Handling</a></li><li><a href="#">Feedback</a></li></ul>';
}
