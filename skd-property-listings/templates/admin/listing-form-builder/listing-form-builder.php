<div class="wrap">
    <h1><?php esc_html_e('Professional Form Builder', 'skd-property-listings'); ?></h1>

    <div class="form-builder">
        <!-- Preset Fields -->
        <div class="preset-fields">
            <h3><?php esc_html_e('Available Fields', 'skd-property-listings'); ?></h3>
            <ul id="preset-fields">
                <li data-type="text"><?php esc_html_e('Text Field', 'skd-property-listings'); ?></li>
                <li data-type="textarea"><?php esc_html_e('Textarea', 'skd-property-listings'); ?></li>
                <li data-type="number"><?php esc_html_e('Number', 'skd-property-listings'); ?></li>
                <li data-type="url"><?php esc_html_e('URL', 'skd-property-listings'); ?></li>
                <li data-type="date"><?php esc_html_e('Date Picker', 'skd-property-listings'); ?></li>
                <li data-type="time"><?php esc_html_e('Time Picker', 'skd-property-listings'); ?></li>
                <li data-type="select"><?php esc_html_e('Dropdown', 'skd-property-listings'); ?></li>
                <li data-type="checkbox"><?php esc_html_e('Checkbox', 'skd-property-listings'); ?></li>
                <li data-type="radio"><?php esc_html_e('Radio Button', 'skd-property-listings'); ?></li>
                <li data-type="file"><?php esc_html_e('File Upload', 'skd-property-listings'); ?></li>
            </ul>
        </div>

        <!-- Active Fields -->
        <div class="active-fields">
            <h3><?php esc_html_e('Your Form', 'skd-property-listings'); ?></h3>
            <ul id="form-fields" class="sortable-fields">
                <li class="no-fields"><?php esc_html_e('Drag a field here to start building your form.', 'skd-property-listings'); ?></li>
            </ul>
        </div>

        <!-- Form Preview -->
        <div class="form-preview">
            <h3><?php esc_html_e('Form Preview', 'skd-property-listings'); ?></h3>
            <div id="form-preview-content"></div>
        </div>
    </div>

    <button id="save-fields" class="button button-primary"><?php esc_html_e('Save Form', 'skd-property-listings'); ?></button>
</div>