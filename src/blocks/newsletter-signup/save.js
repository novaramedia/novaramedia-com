/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * For dynamic blocks that use server-side rendering (render.php), this should
 * return null. The block attributes are still saved as a JSON comment in the
 * post content, which PHP can access via the $attributes parameter.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {null} Null for dynamic rendering via PHP.
 */
export default function save() {
  return null;
}
