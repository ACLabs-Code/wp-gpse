import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
import './index.css';

const Edit = () => {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<div className="gpse-block-placeholder">
				<span className="dashicons dashicons-welcome-widgets-menus"></span>
				{ __( 'GPSE Search Results', 'gpse' ) }
			</div>
		</div>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
