<?php
/**
 * Front-page sequence: the spacing between consecutive front-page sections.
 *
 * Every section declares a frame in the block registry (or, for the two
 * hardcoded bookends, in front-page.php):
 *   - 'contained' — has a visible edge of its own: a coloured rounded box or a
 *                   full-bleed coloured band.
 *   - 'open'      — content sits directly on the page background.
 *
 * Sections carry no outer vertical margin. The gap between two neighbours is a
 * join, chosen from the pair of frames:
 *   contained + contained → 'tight'   (1rem, the gutter)
 *   contained + open      → 'loose'   (2rem)
 *   open + open           → 'divided' (2rem, 1px grey rule at container width, 2rem)
 *
 * NM_Front_Page_Sequence renders sections in order, buffers each one, skips any
 * that produce no output (so a hidden section earns no gap), and emits the join
 * before each rendered section after the first. Joins use padding, never
 * margin, so nothing collapses. Tune the values in
 * nm_get_front_page_join_classes() — it is the only place they live.
 *
 * See docs/architecture/front-page-joins.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Resolves a layout slug to its frame.
 *
 * Static blocks read the registry. Newsletter signups — the only dynamic
 * family — are always contained: partials/email-signup.php draws its box
 * unless a caller opts out, and the front page does not. Unknown slugs render
 * nothing (see nm_render_front_page_block()), so their frame never reaches a join.
 *
 * @param string $slug Layout slug.
 * @return string 'contained' or 'open'.
 */
function nm_get_front_page_block_frame( $slug ) {
  if ( ! is_string( $slug ) || $slug === '' ) {
    return 'contained';
  }

  $registry = nm_get_front_page_block_registry();

  if ( isset( $registry[ $slug ]['frame'] ) ) {
    return $registry[ $slug ]['frame'];
  }

  return 'contained'; // newsletter signups and anything unknown
}

/**
 * Picks the join between two neighbouring frames.
 *
 * @param string $previous_frame Frame of the section above.
 * @param string $next_frame     Frame of the section below.
 * @return string 'tight', 'loose' or 'divided'.
 */
function nm_get_front_page_join_kind( $previous_frame, $next_frame ) {
  if ( $previous_frame === 'open' && $next_frame === 'open' ) {
    return 'divided';
  }

  if ( $previous_frame === 'contained' && $next_frame === 'contained' ) {
    return 'tight';
  }

  return 'loose';
}

/**
 * Utility classes for a join. The single place the front-page rhythm is tuned.
 * Spacing scale: 4 = 1rem, 5 = 2rem, 6 = 3rem. Responsive variants (pt-s-3 …)
 * are also utilities — add them here, not in the partials.
 *
 * @param string $kind 'tight', 'loose' or 'divided'.
 * @return string Space-separated class list.
 */
function nm_get_front_page_join_classes( $kind ) {
  $classes = array(
    'tight'   => 'pt-4',
    'loose'   => 'pt-5',
    'divided' => 'container pt-5 pb-5',
  );

  return isset( $classes[ $kind ] ) ? $classes[ $kind ] : $classes['loose'];
}

/**
 * Echoes a join. 'divided' carries the library-styled <hr> (1px, gray-light).
 *
 * @param string $kind 'tight', 'loose' or 'divided'.
 * @return void
 */
function nm_render_front_page_join( $kind ) {
  echo '<div class="front-page-join front-page-join--' . esc_attr( $kind ) . ' ' . esc_attr( nm_get_front_page_join_classes( $kind ) ) . '" data-testid="front-page-join" data-join="' . esc_attr( $kind ) . '">';

  if ( $kind === 'divided' ) {
    echo '<hr>';
  }

  echo '</div>';
}

/**
 * Renders front-page sections in order with the correct join between each
 * rendered pair. One instance per page render; front-page.php drives it.
 */
final class NM_Front_Page_Sequence {
  /**
   * Frame of the last section that produced output, or null before the first.
   *
   * @var string|null
   */
  private $previous_frame = null;

  /**
   * Renders one section, preceded by its join.
   *
   * The callback is buffered so a section that has nothing to show (and
   * returns before emitting markup) is skipped entirely — no wrapper, no join.
   *
   * @param string   $frame  'contained' or 'open'.
   * @param callable $render Echoes the section (typically a get_template_part() call).
   * @return void
   */
  public function render( $frame, callable $render ) {
    ob_start();
    $render();
    $html = ob_get_clean();

    if ( trim( $html ) === '' ) {
      return;
    }

    if ( $this->previous_frame !== null ) {
      nm_render_front_page_join( nm_get_front_page_join_kind( $this->previous_frame, $frame ) );
    }

    echo '<div class="front-page-section" data-testid="front-page-section" data-frame="' . esc_attr( $frame ) . '">';
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- buffered template output, escaped at source.
    echo '</div>';

    $this->previous_frame = $frame;
  }
}
