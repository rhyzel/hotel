document.addEventListener('DOMContentLoaded', ()=>{
  const fetchBtn = document.getElementById('tax_fetch_btn');
  const empInput = document.getElementById('tax_employee_id');
  const nameEl = document.getElementById('tax_employee_name');
  const sssEl = document.getElementById('tax_sss');
  const philEl = document.getElementById('tax_phil');
  const pagibigEl = document.getElementById('tax_pagibig');
  const withholdEl = document.getElementById('tax_withholding');
  const computeBtn = document.getElementById('tax_compute_btn');
  const saveBtn = document.getElementById('tax_save_btn');

  const messageBox = document.getElementById('taxMessage');
  let messageTimeout = null;
  function showMessage(text, type = 'success') {
    if (!messageBox) { console.log(text); return; }
    if (messageTimeout) { clearTimeout(messageTimeout); messageTimeout = null; }
    messageBox.hidden = false;
    messageBox.textContent = text;
    messageBox.classList.remove('message--error', 'message--success');
    messageBox.classList.add(type === 'error' ? 'message--error' : 'message--success', 'show');
    messageTimeout = setTimeout(() => { messageBox.classList.remove('show', 'message--error', 'message--success'); messageBox.hidden = true; messageTimeout = null; }, 5200);
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
    const empId = empInput.value.trim();
  if(!empId) { showMessage('Enter Employee ID', 'error'); return; }
    try{
      const summary = await fetchSummary(empId);
      // try to fetch name separately via fetch action
      const fetchRes = await fetch('/HRM/hrm_payroll/salary_processing/salary_processing_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: `action=fetch&employee_id=${encodeURIComponent(empId)}` });
      const fetchJson = await fetchRes.json();
      if(fetchJson.status === 'success'){
        const emp = fetchJson.data;
        nameEl.value = `${emp.first_name||''} ${emp.last_name||''}`.trim();
      }
      if(summary && summary.statutory){
        sssEl.value = parseFloat(summary.statutory.sss||0).toFixed(2);
        philEl.value = parseFloat(summary.statutory.philhealth||0).toFixed(2);
        pagibigEl.value = parseFloat(summary.statutory.pagibig||0).toFixed(2);
        withholdEl.value = parseFloat(summary.statutory.withholding||0).toFixed(2);
  showMessage('Tax breakdown loaded', 'success');
      } else {
  showMessage('No statutory data', 'error');
      }
    }catch(err){
  console.error(err);
  showMessage('Error fetching tax data: '+err.message, 'error');
    }
  }

  fetchBtn.addEventListener('click', fetchEmployee);
  empInput.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ e.preventDefault(); fetchEmployee(); } });

  computeBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    const sss = parseFloat(sssEl.value)||0;
    const phil = parseFloat(philEl.value)||0;
    const pagibig = parseFloat(pagibigEl.value)||0;
    const tax = parseFloat(withholdEl.value)||0;
    const total = sss + phil + pagibig + tax;
  showMessage(`Total statutory deductions: ₱ ${total.toFixed(2)}`, 'success');
  });

  saveBtn.addEventListener('click', async (e)=>{
    e.preventDefault();
    const empId = empInput.value.trim();
  if(!empId){ showMessage('Enter Employee ID', 'error'); return; }
    const sss = parseFloat(sssEl.value)||0;
    const phil = parseFloat(philEl.value)||0;
    const pagibig = parseFloat(pagibigEl.value)||0;
    const tax = parseFloat(withholdEl.value)||0;
    const payload = new URLSearchParams();
    payload.append('employee_id', empId);
    payload.append('sss', sss);
    payload.append('philhealth', phil);
    payload.append('pagibig', pagibig);
    payload.append('tax', tax);

    try{
      const res = await fetch('/HRM/hrm_payroll/tax_deduction/tax_deduction_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload.toString() });
      const json = await res.json();
  if(json.status === 'success') showMessage('Tax record saved', 'success'); else showMessage('Save failed: '+(json.message||'unknown'), 'error');
  }catch(err){ console.error(err); showMessage('Save error: '+err.message, 'error'); }
  });
});
