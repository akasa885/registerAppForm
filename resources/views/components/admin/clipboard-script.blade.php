@props([
    'is_tooltip' => true,
])
@push('scripts')
    @if ($is_tooltip)
        <script>
            let copyClipboard = (el) => {
                let link = event.target.dataset.link;
                let id = event.target.id;
                let idInput = `${id}-input`;

                // show tooltip copied
                // change tooltip title
                event.target.dataset.originalTitle = "Copied!";
                event.target.title = "Copied!";

                let tooltip = new bootstrap.Tooltip(el, {
                    trigger: 'manual',
                    placement: 'top'
                });

                let copyText = document.getElementById(idInput);
                copyText.select();
                copyText.setSelectionRange(0, 99999);

                document

                try {
                    if (window.isSecureContext && navigator.clipboard) {
                        navigator.clipboard.writeText(copyText.value);
                    } else {
                        let textArea = document.createElement("textarea");
                        textArea.value = copyText.value;
                        textArea.style.position = "fixed";
                        textArea.style.top = 0;
                        textArea.style.left = 0;
                        textArea.style.width = "2em";
                        textArea.style.height = "2em";
                        textArea.style.padding = 0;
                        textArea.style.border = "none";
                        textArea.style.outline = "none";
                        textArea.style.boxShadow = "none";
                        textArea.style.background = "transparent";
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();

                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    }
                } catch (error) {
                    console.log(error);
                }

                tooltip.show();

                setTimeout(() => {
                    tooltip.hide();
                }, 1000);

                // change tooltip title
                event.target.dataset.originalTitle = "Click to Copy!";
                event.target.title = "Click to Copy!";

                return false;
            }
        </script>
    @endif
@endpush
