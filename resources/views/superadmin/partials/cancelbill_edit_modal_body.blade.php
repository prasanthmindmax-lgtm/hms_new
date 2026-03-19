<input type="hidden" class="userid" name="ref_id" id="edit_disid" value="">
<div class="form-border">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <div class="form-title">Dr. ARAVIND's IVF</div>
            <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
        </div>
        <div class="text-center align-self-center px-3 border"><strong>CANCEL BILL FORM</strong></div>
        <div class="align-self-start"></div>
    </div>
    <input type="hidden" id="locationid">
    <div class="form-row-line mb-2">
        <div class="form-label-col1">OP No</div>
        <div class="form-colon1">:</div>
        <input type="text" class="form-input1" id="opno_edit" readonly>
        <div style="width:36%"></div>
        <div class="form-label-col1">Token No</div>
        <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="token_no errorss"></span>
        <input type="text" class="form-input1" id="token_no_edit">
    </div>
    <div class="form-row-line mb-2">
        <div class="form-label-col1">Consultant</div>
        <div class="form-colon1">:</div>
        <input type="text" class="form-input1" id="consultant_edit" readonly>
        <div style="width:36%"></div>
        <div class="form-label-col1">Bill No</div>
        <div class="form-colon1">:</div>
        <input type="text" class="form-input1" id="billno_edit" readonly>
    </div>
    <div class="form-row-line mb-2">
        <div class="form-label-col1">Date</div>
        <div class="form-colon1">:</div>
        <input type="date" class="form-input1" id="bill_date_edit" readonly>
        <div style="width:36%"></div>
        <div class="form-label-col1">Branch Name</div>
        <div class="form-colon1">:</div>
        <input type="text" class="form-input1" id="zone_id_edit" readonly>
    </div>
    <hr>
    <div class="form-row-line mb-2">
        <div class="form-label-col">Name : <br><input type="text" class="form-input3" id="pat_name_edit" readonly></div>
        <div class="form-label-col">MRD No(ID) : <br><input type="text" class="form-input3" id="pat_mrdno_edit" readonly></div>
        <div class="form-label-col">Age : <br><input type="text" class="form-input3" id="pat_age_edit" readonly></div>
        <div class="form-label-col">Gender : <br>
            <input type="radio" class="form-check-input" id="female_edit" name="pat_gender_edit" value="F" disabled><label for="female_edit">Female</label>
            <input type="radio" class="form-check-input" id="male_edit" name="pat_gender_edit" value="M" disabled><label for="male_edit">Male</label>
        </div>
        <div class="form-label-col">Mobile : <br><input type="text" class="form-input3" id="pct_mobile_edit" readonly></div>
    </div>
    <hr>
    <div class="form-row-line mb-2">
        <div class="form-label-col1">Payment Type</div>
        <div class="form-colon1">:</div>
        <input type="text" class="form-input1" id="payment_type_edit" readonly>
        <div class="form-label-col1">Payment Details</div>
        <div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="payment errorss"></span>
        <input type="text" class="form-input1" id="payment_details_edit">
        <div class="form-label-col">
            <input type="radio" class="form-check-input" name="request_edit" value="OP" disabled><label for="op_edit">op</label>
            <input type="radio" class="form-check-input" name="request_edit" value="IP" disabled><label for="ip_edit">IP</label>
            <input type="radio" class="form-check-input" name="request_edit" value="Pharmacy" disabled><label for="pharmacy_edit">Pharmacy</label>
        </div>
    </div>
    <div class="form-row-line mb-2">
        <table style="width:100%;" id="product_detials_edit">
            <thead><tr><th>S.No</th><th>Particulars</th><th>Qty</th><th>Rate</th><th>Tax(%)</th><th>Amount</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="form-row-line mb-2"><div style="width:76%"></div><div class="form-label-col4">Total</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="totalamt_edit" readonly></div>
    <div class="form-row-line mb-2"><div style="width:76%"></div><div class="form-label-col4">Previous Balance</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="prebalanceamt_edit" readonly></div>
    <div class="form-row-line mb-2"><div style="width:76%"></div><div class="form-label-col4">Amount Receivable</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="receivableamt_edit" readonly></div>
    <div class="form-row-line mb-2">
        <div class="form-label-col4">Amount (in words)</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="receivedamtword_edit" readonly>
        <div style="width:34%"></div><div class="form-label-col4">Amount Received</div><div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="received errorss"></span><input onchange="calcuation()" type="text" class="form-input4" id="receivedamt_edit">
    </div>
    <div class="form-row-line mb-2">
        <div class="form-label-col4">Advance(in words)</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="advancedamtword_edit" readonly>
        <div style="width:34%"></div><div class="form-label-col4">Advance</div><div class="form-colon1">:</div><input type="text" class="form-input4" id="advancedamt_edit" readonly>
    </div>
    <div class="form-row-line mb-2"><div style="width:76%"></div><div class="form-label-col4">Prepared By</div><div class="form-colon1">:</div><span style="font-size:10px; color:red;" class="prepared errorss"></span><input type="text" class="form-input4" id="prepared_edit"></div>
    <div class="form-row-line mb-2">
        <div class="form-label-col" style="width:100%">Cancel Reason : <span style="font-size:10px; color:red;" class="reason errorss"></span>
            <textarea rows="4" class="form-input" style="border: 1px solid #e7eaee;" id="cancelreason_edit"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label>Admin Sign</label>
            <div class="signature-option-group mb-2" data-target="editadmin">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="editadmin-signature" value="upload" id="adminUpload" checked>
                    <label class="form-check-label" for="adminUpload">Upload Image</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="editadmin-signature" value="draw" id="adminDraw">
                    <label class="form-check-label" for="adminDraw">Digital Sign</label>
                </div>
            </div>
            <div class="avatar-upload" id="editadmin-upload" style="margin: 0px;">
                <i class="fa fa-close clear-sign" data-target="editadmin" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                <div class="avatar-edit" style="position: relative;">
                    <input type="file" id="adminsignimge" accept=".png, .jpg, .jpeg" />
                    <label for="adminsignimge"></label>
                </div>
                <div class="avatar-preview" style="width: 200px;">
                    <div id="adminimgPreviewe"></div>
                </div>
            </div>
            <div class="digital-sign mt-2" id="editadmin-sign" style="display:none;">
                <canvas id="editadminCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editadminCanvas')">Clear</button>
            </div>
        </div>
        <div class="col-md-4 hide" id="zonal_sign_edit">
            <label>Zonal Sign</label>
            <div class="signature-option-group mb-2" data-target="editcc">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="editcc-signature" value="upload" id="ccUpload" checked>
                    <label class="form-check-label" for="ccUpload">Upload Image</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="editcc-signature" value="draw" id="ccDraw">
                    <label class="form-check-label" for="ccDraw">Digital Sign</label>
                </div>
            </div>
            <div class="avatar-upload" id="editcc-upload" style="margin: 0px;">
                <i class="fa fa-close clear-sign" data-target="editcc" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                <div class="avatar-edit" style="position: relative;">
                    <input type="file" id="ccsignimge" accept=".png, .jpg, .jpeg" />
                    <label for="ccsignimge"></label>
                </div>
                <div class="avatar-preview" style="width: 200px;">
                    <div id="ccimgPreviewe"></div>
                </div>
            </div>
            <div class="digital-sign mt-2" id="editcc-sign" style="display:none;">
                <canvas id="editccCanvas" width="230" height="50" style="border:1px solid #000;"></canvas>
                <button type="button" class="btn btn-outline-danger btn-sm mt-1" onclick="clearCanvas('editccCanvas')">Clear</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" style="height: 34px;width: 133px;font-size: 12px;" id="close-button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
    <button type="submit" id="editcancelform" style="height: 34px;width: 133px;font-size: 12px;" class="btn btn-outline-primary">Submit</button>
</div>
