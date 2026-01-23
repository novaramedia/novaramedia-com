/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes }) {
	if (!attributes.newsletter) {
		return null;
	}

	return (
		<div {...useBlockProps.save()}>
			<div className="grid-row">
				<div className="grid-item is-xxl-24">
					<h3 className="fs-8 fs-s-6 mb-4 js-fix-widows">Headline</h3>
					<p className="fs-6 fs-s-4-sans mr-6">Copy</p>
				</div>
				<div className="grid-item is-xxl-24">
					<form
						className="email-signup__form"
						action="netlify_url"
						method="post"
						target="_blank"
					>
						<input
							type="hidden"
							name="newsletter"
							value="mailchimp_key"
						/>

						<div className="email-signup__inputs">
							<div className="form-group mb-2">
								<label className="u-visuallyhidden" htmlFor="firstName">
									First name:
								</label>
								<input
									name="firstName"
									className="email-signup__name-input ui-input"
									id="firstName"
									type="text"
									autoComplete="given-name"
									placeholder="First name"
								/>
							</div>

							<div className="form-group mb-2">
								<label className="u-visuallyhidden" htmlFor="email">
									Email:
								</label>
								<input
									name="email"
									className="email-signup__email-input ui-input"
									id="email"
									type="email"
									autoComplete="email"
									placeholder="Email"
									required
								/>
							</div>

							<div className="email-signup__email-gdpr-group form-group layout-flex-align-center mb-2">
								<label htmlFor="newsletter-gdpr" className="fs-2">
									I agree to the{' '}
									<a
										target="_blank"
										rel="noopener noreferrer"
										href="/privacy-policy"
									>
										Privacy Policy
									</a>
								</label>
								<input
									name="gdpr"
									className="email-signup__email-gdpr-input ui-checkbox ml-2"
									id="newsletter-gdpr"
									type="checkbox"
									value="accepted"
									required
								/>
							</div>

							<input
								className="email-signup__submit ui-button ui-button--black fs-6"
								type="submit"
								value="Sign up"
							/>

							<span className="email-signup__feedback-processing ui-button ui-button--disabled fs-6">
								Processing...
							</span>
							<span className="email-signup__feedback-completed ui-button ui-button--disabled fs-6">
								Success
							</span>
							<div className="email-signup__feedback-failed layout-split-level">
								<input
									className="ui-button ui-button--black fs-6"
									type="submit"
									value="Try again"
								/>
								<p className="ml-2 fs-2">
									Failed: <span className="email-signup__feedback-message"></span>.
								</p>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	);
}
