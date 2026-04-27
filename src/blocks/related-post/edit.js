import { useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, TextControl, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { decodeEntities } from '@wordpress/html-entities';

import './editor.scss';

const SEARCH_DEBOUNCE_MS = 250;

export default function Edit({ attributes, setAttributes }) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);
  const [isSearching, setIsSearching] = useState(false);
  const debounceRef = useRef(null);
  const requestRef = useRef(0);

  const blockProps = useBlockProps({
    className: 'wp-block-nm-wp-related-post',
    style: {
      color: '#222222',
      backgroundColor: '#eeeeee',
      borderRadius: '5px',
      padding: '2rem',
    },
  });

  const { relatedPost } = attributes;

  useEffect(() => {
    if (debounceRef.current) {
      clearTimeout(debounceRef.current);
    }

    if (!query || query.trim().length < 2) {
      setResults([]);
      setIsSearching(false);
      return undefined;
    }

    setIsSearching(true);
    const requestId = ++requestRef.current;

    debounceRef.current = setTimeout(() => {
      apiFetch({
        path: addQueryArgs('/wp/v2/search', {
          search: query,
          subtype: 'post,event',
          per_page: 25,
          _fields: 'id,title,subtype',
        }),
      })
        .then((items) => {
          if (requestId !== requestRef.current) {
            return;
          }
          setResults(Array.isArray(items) ? items : []);
          setIsSearching(false);
        })
        .catch((error) => {
          if (requestId !== requestRef.current) {
            return;
          }
          console.error('Related post search failed:', error);
          setResults([]);
          setIsSearching(false);
        });
    }, SEARCH_DEBOUNCE_MS);

    return () => {
      if (debounceRef.current) {
        clearTimeout(debounceRef.current);
      }
    };
  }, [query]);

  const selectPost = (item) => {
    setAttributes({
      relatedPost: {
        id: item.id,
        title: typeof item.title === 'string' ? item.title : item.title?.rendered || '',
        postType: item.subtype,
      },
    });
    setQuery('');
    setResults([]);
  };

  const clearSelection = () => {
    setAttributes({ relatedPost: null });
  };

  return (
    <div {...blockProps}>
      <h3 style={{ marginTop: 0 }}>{__('Related Post', 'novaramedia-com')}</h3>

      {!relatedPost && (
        <>
          <p>
            {__(
              'Search for a post or event to embed as a related item.',
              'novaramedia-com'
            )}
          </p>
          <TextControl
            label={__('Search', 'novaramedia-com')}
            value={query}
            onChange={setQuery}
            placeholder={__('Type at least 2 characters…', 'novaramedia-com')}
          />
          {isSearching && <Spinner />}
          {!isSearching && results.length > 0 && (
            <ul className="nm-block-related-post__results">
              {results.map((item) => (
                <li key={item.id}>
                  <button
                    type="button"
                    className="nm-block-related-post__result"
                    onClick={() => selectPost(item)}
                  >
                    {decodeEntities(
                      typeof item.title === 'string'
                        ? item.title
                        : item.title?.rendered || ''
                    )}
                    <span className="nm-block-related-post__type">
                      {item.subtype}
                    </span>
                  </button>
                </li>
              ))}
            </ul>
          )}
          {!isSearching && query.trim().length >= 2 && results.length === 0 && (
            <p style={{ fontSize: '0.875rem', color: '#666' }}>
              {__('No matches.', 'novaramedia-com')}
            </p>
          )}
        </>
      )}

      {relatedPost && (
        <div className="nm-block-related-post__selected">
          <div>
            <strong>{decodeEntities(relatedPost.title)}</strong>
            <span className="nm-block-related-post__type">
              {relatedPost.postType}
            </span>
          </div>
          <Button variant="secondary" onClick={clearSelection}>
            {__('Change', 'novaramedia-com')}
          </Button>
        </div>
      )}
    </div>
  );
}
