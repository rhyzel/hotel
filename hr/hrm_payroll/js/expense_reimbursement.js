document.addEventListener('DOMContentLoaded', ()=>{
  const fetchBtn = document.getElementById('exp_fetch_btn');
  const empInput = document.getElementById('exp_employee_id');
  const nameEl = document.getElementById('exp_employee_name');
  const typeEl = document.getElementById('exp_type');
  const amountEl = document.getElementById('exp_amount');
  const remarksEl = document.getElementById('exp_remarks');
  const verifyBtn = document.getElementById('exp_verify_btn');
  const saveBtn = document.getElementById('exp_save_btn');
  const messageBox = document.getElementById('expenseMessage');
  let messageTimeout = null;

  function showMessage(text, type='success'){
    if(!messageBox){ console.log(text); return; }
    if(messageTimeout) { clearTimeout(messageTimeout); messageTimeout = null; }
    messageBox.hidden = false; messageBox.textContent = text;
    messageBox.classList.remove('message--error','message--success');
    messageBox.classList.add(type==='error' ? 'message--error' : 'message--success','show');
    messageTimeout = setTimeout(()=>{ messageBox.classList.remove('show','message--error','message--success'); messageBox.hidden = true; messageTimeout=null; },5200);
  }

  async function fetchSummary(employeeId){
    if(!employeeId) return null;
    const payload = new URLSearchParams(); payload.append('action','summary'); payload.append('employee_id', employeeId);
    const res = await fetch('/HRM/hrm_payroll/salary_processing/salary_processing_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload.toString() });
    if(!res.ok) throw new Error('Network error');
    const json = await res.json();
    if(json.status !== 'success') throw new Error(json.message||'Summary failed');
    return json.data;
  }

  async function fetchEmployee(){
    const empId = empInput.value.trim(); if(!empId){ showMessage('Enter Employee ID','error'); return; }
    try{
      const fetchRes = await fetch('/HRM/hrm_payroll/salary_processing/salary_processing_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: `action=fetch&employee_id=${encodeURIComponent(empId)}` });
      const fetchJson = await fetchRes.json();
      if(fetchJson.status === 'success'){ const emp = fetchJson.data; nameEl.value = `${emp.first_name||''} ${emp.last_name||''}`.trim(); showMessage('Employee loaded','success'); }
      // also preview if there are outstanding reimbursements or summary
      try{ const summary = await fetchSummary(empId); if(summary){ /* optionally show contextual info later */ } }catch(e){ /* ignore */ }
    }catch(err){ console.error(err); showMessage('Error fetching: '+err.message,'error'); }
  }

  fetchBtn.addEventListener('click', fetchEmployee);
  empInput.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ e.preventDefault(); fetchEmployee(); } });

  verifyBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    const type = typeEl.value.trim();
    const amount = parseFloat(amountEl.value) || 0;
    if(!empInput.value.trim()){ showMessage('Enter employee id','error'); return; }
    if(!type){ showMessage('Provide expense type','error'); return; }
    if(amount <= 0){ showMessage('Enter valid amount','error'); return; }
    // simple business rule: amount must be >0 and less than a threshold (example)
    if(amount > 50000){ showMessage('Amount exceeds single-claim limit of ₱50,000','error'); return; }
    showMessage('Expense verified: ₱ '+amount.toFixed(2),'success');
  });

  saveBtn.addEventListener('click', async (e)=>{
    e.preventDefault();
    const empId = empInput.value.trim(); const type = typeEl.value.trim(); const amount = parseFloat(amountEl.value) || 0; const remarks = remarksEl.value.trim();
    if(!empId){ showMessage('Enter employee id','error'); return; }
    if(!type){ showMessage('Provide expense type','error'); return; }
    if(amount <= 0){ showMessage('Enter valid amount','error'); return; }
    const payload = new URLSearchParams();
    payload.append('employee_id', empId);
    payload.append('expense_type', type);
    payload.append('amount', amount);
    payload.append('description', remarks);

    try{
      const res = await fetch('/HRM/hrm_payroll/expense_reimbursement/expense_reimbursement_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload.toString() });
      const json = await res.json();
      if(json.status === 'success'){
        showMessage('Expense reimbursement saved','success');
        // clear form
        typeEl.value=''; amountEl.value=''; remarksEl.value='';
      } else {
        showMessage('Save failed: '+(json.message||'unknown'),'error');
      }
    }catch(err){ console.error(err); showMessage('Save error: '+err.message,'error'); }
  });
});
