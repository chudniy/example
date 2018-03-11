<?php
  /**
   * The template for displaying the footer
   * Contains the closing of the #content div and all content after.
   *
   * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
   * @package WordPress
   * @subpackage Twenty_Seventeen
   * @since 1.0
   * @version 1.2
   */

?>

</div>
<footer class = "main_footer <?php if (!is_front_page()): ?> main_footer-bordered <?php endif; ?>">
	<?php wp_footer(); ?>
</footer>
</body>
</html>
