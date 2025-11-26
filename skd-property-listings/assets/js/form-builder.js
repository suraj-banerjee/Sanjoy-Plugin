(function ($) {
    $(document).ready(function () {
        const formFields = $("#form-fields");
        const formPreviewContainer = $("#form-preview-content");
        const saveButton = $("#save-fields");

        // Add "Business Name" field as a default field
        const businessNameField = createFieldItem("text", "Business Name");
        formFields.prepend(businessNameField); // Insert the field at the top
        initFieldActions(businessNameField); // Initialize actions for the Business Name field

        // Initialize sortable functionality
        formFields.sortable({
            placeholder: "sortable-placeholder",
            items: "> li:not(.no-fields):not(.business-name)",  // Exclude the `no-fields` item from sorting
            update: function () {
                updateFormPreview(); // Update the form preview when fields are rearranged
            },
            receive: function (event, ui) {
                // Get the field type from the dragged element
                const fieldType = ui.helper.data("type");

                // Create a new field item
                const newField = createFieldItem(fieldType);

                // Replace the placeholder with the new field
                ui.helper.replaceWith(newField);

                // Initialize actions for the new field
                initFieldActions(newField);

                // Update the form preview
                updateFormPreview();

                // Remove the automatically added `<li>` with only the field name
                // $(ui.helper).remove();
            },
        });

        // Enable dragging fields from the preset list
        $("#preset-fields li").draggable({
            connectToSortable: "#form-fields", // Allow dragging into the form field container
            helper: "clone", // Clone the element while dragging
            revert: "invalid", // Revert if dropped outside valid container
            start: function () {
                formFields.addClass("highlight-drop-area"); // Highlight the drop area
            },
            stop: function (event, ui) {
                formFields.removeClass("highlight-drop-area"); // Remove highlight after drop
            },
        });

        // Prevent dropping outside the "form-fields" container
        $("body").on("mouseup", function (e) {
            if (!$(e.target).closest("#form-fields").length) {
                $(".ui-draggable-dragging").remove(); // Remove dragged element
            }
        });

        // Prevent dragging and deleting the "Business Name" field
        formFields.on("click", ".delete-field", function () {
            if ($(this).closest(".active-field").hasClass("business-name")) {
                alert("You cannot remove the Business Name field.");
                return false; // Prevent deletion
            }
        });

        // Prevent dragging of the "Business Name" field
        formFields.on("mousedown", ".business-name", function (e) {
            e.preventDefault(); // Prevent dragging
        });

        // Create a new field item
        function createFieldItem(fieldType, defaultLabel = "") {
            const label = defaultLabel || fieldType.charAt(0).toUpperCase() + fieldType.slice(1); // Default label based on field type

            return $(`
                <li class="active-field ${defaultLabel === "Business Name" ? 'business-name' : ''}" data-type="${fieldType}">
                    <div class="field-preview">
                        <span class="field-label">${label}</span>
                        <span class="field-type">(${fieldType})</span> <!-- Field type in brackets -->
                        <div class="field-actions">
                            <button class="edit-field">Edit</button>
                            <button class="delete-field">Delete</button>
                        </div>
                    </div>
                    <div class="field-settings" style="display: none;">
                        <label>Field Label: <input type="text" class="field-label-input" value="${label}" /></label>
                        <label>Placeholder: <input type="text" class="field-placeholder-input" /></label>
                        <label>Required: <input type="checkbox" class="field-required-input" /></label>
                        ${fieldType === 'select' || fieldType === 'radio' || fieldType === 'checkbox'
                    ? `
                            <ul class="options-list"></ul>
                            <input type="text" class="option-input" placeholder="Option text" />
                            <button class="add-option">Add Option</button>
                            `
                    : ''
                }
                        <button class="save-field-settings">Save</button>
                    </div>
                </li>
            `);
        }

        // Initialize field actions (edit, delete, save)
        function initFieldActions(field) {
            field.find(".edit-field").on("click", function () {
                field.find(".field-settings").slideToggle();
            });

            field.find(".delete-field").on("click", function () {
                if (field.hasClass("business-name")) {
                    alert("You cannot remove this field.");
                    return; // Prevent deletion if it's the "Business Name" field
                }
                field.remove();
                updateFormPreview(); // Update preview after field is removed
            });

            field.find(".save-field-settings").on("click", function () {
                const label = field.find(".field-label-input").val();
                field.find(".field-label").text(label); // Update label in preview
                field.data("placeholder", field.find(".field-placeholder-input").val());
                field.find(".field-settings").slideUp();
                updateFormPreview(); // Update preview after saving field settings
            });

            field.find(".add-option").on("click", function () {
                const optionText = field.find(".option-input").val().trim();
                if (optionText) {
                    // Add option to the list
                    field.find(".options-list").append(`
                        <li>${optionText} <button class="remove-option">Remove</button></li>
                    `);
                    field.find(".option-input").val(""); // Clear input field
                    updateFormPreview(); // Update preview after adding an option
                }
            });

            field.on("click", ".remove-option", function () {
                $(this).closest("li").remove();
                updateFormPreview(); // Update preview after removing an option
            });
        }

        // Update form preview
        function updateFormPreview() {
            formPreviewContainer.empty(); // Clear the preview area

            formFields.find(".active-field").each(function () {
                const fieldType = $(this).data("type");
                const label = $(this).find(".field-label").text();

                let fieldHtml = '';

                // Generate preview HTML based on field type
                switch (fieldType) {
                    case 'text':
                        fieldHtml = `<label>${label}</label><input type="text" placeholder="Enter ${label}" />`;
                        break;
                    case 'textarea':
                        fieldHtml = `<label>${label}</label><textarea placeholder="Enter ${label}"></textarea>`;
                        break;
                    case 'number':
                        fieldHtml = `<label>${label}</label><input type="number" placeholder="Enter ${label}" />`;
                        break;
                    case 'url':
                        fieldHtml = `<label>${label}</label><input type="url" placeholder="Enter ${label}" />`;
                        break;
                    case 'date':
                        fieldHtml = `<label>${label}</label><input type="date" />`;
                        break;
                    case 'time':
                        fieldHtml = `<label>${label}</label><input type="time" />`;
                        break;
                    case 'select':
                        const selectOptions = $(this).find(".options-list li");
                        let selectHtml = `<label>${label}</label><select>`;
                        selectOptions.each(function () {
                            const optionText = $(this).clone().children().remove().end().text().trim(); // Extract only option text
                            selectHtml += `<option value="${optionText}">${optionText}</option>`;
                        });
                        selectHtml += `</select>`;
                        fieldHtml = selectHtml;
                        break;
                    case 'checkbox':
                        const checkboxOptions = $(this).find(".options-list li");
                        let checkboxHtml = `<label>${label}</label>`;
                        checkboxOptions.each(function () {
                            const optionText = $(this).clone().children().remove().end().text().trim(); // Extract only option text
                            checkboxHtml += `<label><input type="checkbox" /> ${optionText}</label>`;
                        });
                        fieldHtml = checkboxHtml;
                        break;
                    case 'radio':
                        const radioOptions = $(this).find(".options-list li");
                        let radioHtml = `<label>${label}</label>`;
                        radioOptions.each(function () {
                            const optionText = $(this).clone().children().remove().end().text().trim(); // Extract only option text
                            radioHtml += `<label><input type="radio" name="${label}" /> ${optionText}</label>`;
                        });
                        fieldHtml = radioHtml;
                        break;
                    case 'file':
                        fieldHtml = `<label>${label}</label><input type="file" />`;
                        break;
                }

                // Append generated field HTML to the preview container
                formPreviewContainer.append(`<div class="preview-field">${fieldHtml}</div>`);
            });
        }

        // Fetch existing form structure on load
        $.ajax({
            url: skdFormBuilderAjax.ajaxurl,
            type: "GET",
            data: {
                action: "get_form_builder_fields",
            },
            success: function (response) {
                if (response.success) {
                    response.data.forEach(function (field) {
                        const fieldHtml = `
                            <li class="active-field" data-type="${field.field_type}" data-id="${field.id}">
                                <span class="field-label">${field.field_name}</span>
                                <input type="text" class="field-placeholder-input" value="${field.field_placeholder}" />
                                <input type="checkbox" class="field-required-input" ${field.field_required ? "checked" : ""} />
                            </li>`;
                        formFields.append(fieldHtml);
                    });
                }
            },
        });

        // Save form structure via AJAX
        saveButton.on("click", function () {
            const fields = [];
            formFields.find(".active-field").each(function (index) {
                fields.push({
                    id: $(this).data("id") || null,
                    field_name: $(this).find(".field-label").text(),
                    field_type: $(this).data("type"),
                    field_placeholder: $(this).find(".field-placeholder-input").val(),
                    field_required: $(this).find(".field-required-input").is(":checked") ? 1 : 0,
                    sort_order: index + 1,
                });
            });
            console.log(fields);

            // Save via AJAX
            $.ajax({
                url: skdFormBuilderAjax.ajaxurl,
                type: "POST",
                data: {
                    action: "save_form_builder_fields",
                    fields: JSON.stringify(fields),
                },
                success: function (response) {
                    alert(response);
                    console.log(response);
                    // if (response.success) {
                    //     alert("Form saved successfully!");
                    // } else {
                    //     alert("Error saving form.");
                    // }
                },
            });
        });
    });
})(jQuery);
