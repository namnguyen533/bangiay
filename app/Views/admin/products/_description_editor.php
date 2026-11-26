<div class="mb-3 description-editor">
    <label class="form-label fw-bold">Mô tả</label>
    <div class="btn-toolbar gap-1 mb-2" role="toolbar" aria-label="Định dạng mô tả">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold" title="In đậm" aria-label="In đậm"><strong>B</strong></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic" title="In nghiêng" aria-label="In nghiêng"><em>I</em></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline" title="Gạch chân" aria-label="Gạch chân"><u>U</u></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList" title="Danh sách dấu đầu dòng" aria-label="Danh sách dấu đầu dòng">• Danh sách</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList" title="Danh sách đánh số" aria-label="Danh sách đánh số">1. Danh sách</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="formatBlock" data-value="&lt;h3&gt;" title="Tiêu đề" aria-label="Tiêu đề">Tiêu đề</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="formatBlock" data-value="&lt;p&gt;" title="Đoạn văn" aria-label="Đoạn văn">Đoạn văn</button>
    </div>
    <div class="form-control description-editor__surface"
         id="product-description-editor"
         contenteditable="true"
         role="textbox"
         aria-label="Nội dung mô tả sản phẩm"
         aria-multiline="true"
         data-placeholder="Nhập mô tả sản phẩm... "
         hidden></div>
    <textarea name="description" class="form-control description-editor__source" rows="5"><?= htmlspecialchars($descriptionEditorContent ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
    <small class="form-text text-muted">Có thể định dạng chữ, tạo danh sách và tiêu đề.</small>
</div>
<style>
    .description-editor__surface {
        min-height: 180px;
        overflow-y: auto;
    }
    .description-editor__surface:empty::before {
        color: #6c757d;
        content: attr(data-placeholder);
        pointer-events: none;
    }
</style>
<script>
(() => {
    const form = document.currentScript.closest('form');
    const wrapper = form.querySelector('.description-editor');
    const source = wrapper.querySelector('.description-editor__source');
    const editor = wrapper.querySelector('.description-editor__surface');

    editor.innerHTML = source.value;
    source.hidden = true;
    editor.hidden = false;

    const syncDescription = () => {
        source.value = editor.innerHTML;
    };

    wrapper.querySelectorAll('[data-command]').forEach(button => {
        button.addEventListener('mousedown', event => event.preventDefault());
        button.addEventListener('click', () => {
            editor.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value || null);
            syncDescription();
        });
    });

    editor.addEventListener('input', syncDescription);
    form.addEventListener('submit', syncDescription);
})();
</script>
