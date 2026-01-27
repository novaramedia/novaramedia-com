import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
  const [newsletterPosts, setNewsletterPosts] = useState(null);

  const blockProps = useBlockProps({
    className: 'wp-block-flavor3-newsletter-signup',
    style: {
      color: '#222222',
      backgroundColor: '#eeeeee',
      borderRadius: '5px',
      padding: '2rem',
    },
  });

  useEffect(() => {
    const fetchNewsletterPosts = async () => {
      const posts = await apiFetch({
        path: addQueryArgs('/wp/v2/newsletter', { per_page: 100 }),
      });
      setNewsletterPosts(posts);
    };

    fetchNewsletterPosts();
  }, []);

  const handleNewsletterChange = (event) => {
    const newsletter = newsletterPosts.find(
      (post) => post.id === parseInt(event.target.value)
    );
    setAttributes({ newsletter });
  };

  const selectedId = attributes.newsletter?.id || '';

  return (
    <div {...blockProps}>
      <h3 style={{ marginTop: 0 }}>
        {__('Newsletter Signup', 'novaramedia-com')}
      </h3>
      <p>
        {__(
          'Select a newsletter. Displays an inline signup form in the content.',
          'novaramedia-com'
        )}
      </p>
      <form>
        <select
          className="nm-block-newsletter-signup__select"
          onChange={handleNewsletterChange}
          value={selectedId}
        >
          <option value="">
            {__('-- Select newsletter --', 'novaramedia-com')}
          </option>
          {newsletterPosts &&
            newsletterPosts.map((post) => (
              <option key={post.id} value={post.id}>
                {post.title.rendered}
              </option>
            ))}
        </select>
      </form>
      {attributes.newsletter && (
        <>
          <p style={{ marginTop: '1rem', fontSize: '0.875rem', color: '#666' }}>
            {__('Selected:', 'novaramedia-com')}{' '}
            {attributes.newsletter.title?.rendered}
          </p>
          <div style={{ marginTop: '1.5rem' }}>
            <h4 style={{ marginBottom: '0.5rem', fontSize: '0.875rem' }}>
              {__('Custom Overrides (Optional)', 'novaramedia-com')}
            </h4>
            <TextControl
              label={__('Custom Title', 'novaramedia-com')}
              value={attributes.customTitle}
              onChange={(value) => setAttributes({ customTitle: value })}
              placeholder={__('Leave empty to use newsletter default', 'novaramedia-com')}
              help={__('Override the title/headline for this newsletter signup', 'novaramedia-com')}
            />
            <TextareaControl
              label={__('Custom Text', 'novaramedia-com')}
              value={attributes.customText}
              onChange={(value) => setAttributes({ customText: value })}
              placeholder={__('Leave empty to use newsletter default', 'novaramedia-com')}
              help={__('Override the description text for this newsletter signup', 'novaramedia-com')}
              rows={3}
            />
          </div>
        </>
      )}
    </div>
  );
}
