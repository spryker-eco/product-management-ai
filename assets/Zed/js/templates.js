const spinnerTemplate = ()=> {
    return `
    <span class="spinner">
        <span class="spinner__inner spinner__inner--top"></span>
        <span class="spinner__inner spinner__inner--bottom"></span>
    </span>
    `
}
export const modalTemplates = ()=> {
    return `
        <div id="modal-loading" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-loading">
                            ${spinnerTemplate()}
                            <span>Translating</span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-translator" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-subtitle">Translated to: <b class="js-translate-to"></b></div>

                        <div class="form-group js-translate-type">
                            <label class="js-translate-label control-label" for="ai-translated"></label>
                            <input type="text" id="ai-translated" class="js-translate-field form-control">
                            <textarea id="ai-translated" class="js-translate-field form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="modal-actions">
                            <button type="button" class="js-translate-again btn btn-outline btn-view">Try again</button>
                            <button type="button" class="js-translate-apply btn" data-dismiss="modal">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
}
