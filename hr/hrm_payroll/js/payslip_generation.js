document.addEventListener('DOMContentLoaded', ()=>{
  const empInput = document.getElementById('payslip_employee_id');
  const nameEl = document.getElementById('payslip_employee_name');
  const periodEl = document.getElementById('payslip_period');
  const genBtn = document.getElementById('payslip_generate_btn');
  const downloadBtn = document.getElementById('payslip_download_btn');
  const fetchBtn = document.getElementById('payslip_fetch_btn');
  const preview = document.getElementById('payslip_preview');
  const previewName = document.getElementById('preview_name');
  const previewEmpId = document.getElementById('preview_empid');
  const previewPeriod = document.getElementById('preview_period');
  const previewLines = document.getElementById('preview_lines');
  const timestampEl = document.getElementById('payslip_timestamp');
  const printBtn = document.getElementById('payslip_print_btn');
  const releaseBtn = document.getElementById('payslip_release_btn');
  const messageBox = document.getElementById('payslipMessage');
  let messageTimeout = null;
  function showMessage(text, type='success'){
    if(!messageBox){ alert(text); return; }
    if(messageTimeout) { clearTimeout(messageTimeout); messageTimeout = null; }
    messageBox.hidden = false; messageBox.textContent = text;
    messageBox.classList.remove('message--error','message--success');
    messageBox.classList.add(type==='error' ? 'message--error' : 'message--success','show');
    messageTimeout = setTimeout(()=>{ messageBox.classList.remove('show','message--error','message--success'); messageBox.hidden = true; messageTimeout=null; },5200);
  }

  // Utility: dynamically load a script and return a Promise that resolves when loaded
  function loadScript(src){
    return new Promise((resolve, reject)=>{
      // if already present, resolve immediately
      if(document.querySelector(`script[src="${src}"]`)) return resolve();
      const s = document.createElement('script'); s.src = src; s.async = true;
      s.onload = ()=>resolve(); s.onerror = (e)=>reject(new Error('Failed to load '+src));
      document.head.appendChild(s);
    });
  }

  // Ensure both html2canvas and jsPDF are available, lazy-loading from CDN if necessary
  async function ensurePdfLibs(){
    const html2cdn = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    const jspdfcdn = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    // html2canvas
    if(!(window.html2canvas || window.html2)){
      try{ await loadScript(html2cdn); }catch(e){ console.warn('html2canvas CDN load failed', e); }
    }
    // jsPDF
    if(!((window.jspdf && window.jspdf.jsPDF) || window.jsPDF)){
      try{ await loadScript(jspdfcdn); }catch(e){ console.warn('jsPDF CDN load failed', e); }
    }
    const html2func = window.html2canvas || window.html2 || null;
    const jsPDFCtor = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF ? window.jsPDF : null);
    return { html2func, jsPDFCtor };
  }

  async function fetchEmployee(empId){
    const res = await fetch('/HRM/hrm_payroll/salary_processing/salary_processing_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: `action=fetch&employee_id=${encodeURIComponent(empId)}` });
    if(!res.ok) throw new Error('Network error');
    const json = await res.json(); if(json.status!=='success') throw new Error(json.message||'not found');
    return json.data;
  }

  async function fetchSummary(empId){
    const payload = new URLSearchParams(); payload.append('action','summary'); payload.append('employee_id', empId);
    const res = await fetch('/HRM/hrm_payroll/salary_processing/salary_processing_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload.toString() });
    if(!res.ok) throw new Error('Network error');
    const json = await res.json(); if(json.status!=='success') throw new Error(json.message||'summary failed');
    return json.data;
  }

  // default period to current month
  (function setDefaultPeriod(){ const d=new Date(); const mm=('0'+(d.getMonth()+1)).slice(-2); const yyyy=d.getFullYear(); periodEl.value = `${yyyy}-${mm}`; })();

  fetchBtn && fetchBtn.addEventListener('click', async ()=>{ const emp = empInput.value.trim(); if(!emp){ showMessage('Enter employee id','error'); return; } try{ const data = await fetchEmployee(emp); nameEl.value = `${data.first_name||''} ${data.last_name||''}`.trim(); showMessage('Employee loaded','success'); }catch(err){ showMessage('Error: '+err.message,'error'); } });

  empInput.addEventListener('keydown',(e)=>{ if(e.key==='Enter'){ e.preventDefault(); fetchBtn && fetchBtn.click(); } });

  genBtn.addEventListener('click', async ()=>{
    const empId = empInput.value.trim(); if(!empId) { showMessage('Enter employee id','error'); return; }
    const period = periodEl.value; if(!period) { showMessage('Select month & year','error'); return; }
    try{
      const emp = await fetchEmployee(empId);
      nameEl.value = `${emp.first_name||''} ${emp.last_name||''}`.trim();
      const summary = await fetchSummary(empId);
      // Build preview lines
      previewName.textContent = nameEl.value;
      previewEmpId.textContent = empId;
      const d = new Date(); timestampEl.textContent = d.toLocaleString();
      // format period YYYY-MM -> display Month Year
      const [yr,mo] = period.split('-');
      const monthName = new Date(yr, mo-1, 1).toLocaleString(undefined,{month:'long', year:'numeric'});
      previewPeriod.textContent = monthName;
      previewLines.innerHTML = '';
      const lines = [];
      const basic = parseFloat(summary.base_salary||0);
      lines.push(`<div><strong>Basic Salary:</strong> ₱ ${basic.toFixed(2)}</div>`);
      if(summary.overtime_pay) lines.push(`<div><strong>Overtime:</strong> ₱ ${parseFloat(summary.overtime_pay).toFixed(2)}</div>`);
      if(summary.bonuses_sum) lines.push(`<div><strong>Bonuses:</strong> ₱ ${parseFloat(summary.bonuses_sum).toFixed(2)}</div>`);
      if(summary.expenses_sum) lines.push(`<div><strong>Expense Reimbursements:</strong> ₱ ${parseFloat(summary.expenses_sum).toFixed(2)}</div>`);
      // statutory breakdown
      if(summary.statutory){
        const s = summary.statutory;
        lines.push(`<div><strong>SSS:</strong> ₱ ${parseFloat(s.sss||0).toFixed(2)}</div>`);
        lines.push(`<div><strong>PhilHealth:</strong> ₱ ${parseFloat(s.philhealth||0).toFixed(2)}</div>`);
        lines.push(`<div><strong>Pag-IBIG:</strong> ₱ ${parseFloat(s.pagibig||0).toFixed(2)}</div>`);
        lines.push(`<div><strong>Withholding:</strong> ₱ ${parseFloat(s.withholding||0).toFixed(2)}</div>`);
      }
      // deductions and net
      const deductions = parseFloat(summary.deductions_sum||0) + (summary.statutory ? (parseFloat(summary.statutory.sss||0)+parseFloat(summary.statutory.philhealth||0)+parseFloat(summary.statutory.pagibig||0)+parseFloat(summary.statutory.withholding||0)) : 0) + parseFloat(summary.absence_penalty||0) - parseFloat(summary.bonuses_sum||0) + parseFloat(summary.expenses_sum||0);
      const gross = basic + parseFloat(summary.overtime_pay||0);
      const net = gross - deductions;
      lines.push(`<hr/>`);
      lines.push(`<div><strong>Gross Pay:</strong> ₱ ${gross.toFixed(2)}</div>`);
      lines.push(`<div><strong>Total Deductions:</strong> ₱ ${deductions.toFixed(2)}</div>`);
      lines.push(`<div style="font-size:18px;margin-top:8px;"><strong>Net Pay:</strong> ₱ ${net.toFixed(2)}</div>`);
      previewLines.innerHTML = lines.join('');
      preview.style.display = 'block';
    }catch(err){ showMessage('Error generating payslip: '+err.message,'error'); }
  });

  printBtn.addEventListener('click', ()=>{
    // Print only the preview area
    const printContents = preview.innerHTML;
    const newWin = window.open('', '', 'width=800,height=600');
    newWin.document.write('<html><head><title>Payslip</title>');
    newWin.document.write('<link rel="stylesheet" href="/HRM/css/css4.css">');
    newWin.document.write('</head><body>');
    newWin.document.write('<div style="padding:20px">'+printContents+'</div>');
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.focus();
    newWin.print();
    newWin.close();
  });

  downloadBtn && downloadBtn.addEventListener('click', async ()=>{
    const empId = empInput.value.trim(); const period = periodEl.value; if(!empId || !period) { showMessage('Ensure employee and period selected','error'); return; }
    if(!preview || preview.style.display==='none'){ showMessage('Generate a preview first','error'); return; }
    try{
      const { html2func, jsPDFCtor } = await ensurePdfLibs();
      if(!html2func){ showMessage('PDF library html2canvas not found. Ensure the script is loaded before this file.','error'); return; }
      if(!jsPDFCtor){ showMessage('PDF library jsPDF not found. Ensure the script is loaded before this file.','error'); return; }

      showMessage('Generating PDF, please wait...', 'success');
      // increase scale for better quality
      const canvas = await html2func(preview, { scale: 2, useCORS: true });
      const imgData = canvas.toDataURL('image/jpeg', 1.0);
  const pdf = new jsPDFCtor('p', 'pt', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const imgWidth = pageWidth;
      const imgHeight = (canvas.height * pageWidth) / canvas.width;

      let position = 0;
      // If content fits on one page, add and save. Otherwise slice into multiple pages.
      if(imgHeight <= pageHeight){
        pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, imgHeight);
      } else {
        // add first page
        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
        let remainingHeight = imgHeight - pageHeight;
        // while there's remaining content, add new pages
        while(remainingHeight > -1){
          position = position - pageHeight;
          pdf.addPage();
          pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
          remainingHeight -= pageHeight;
        }
      }
      const filename = `payslip_${empId}_${period}.pdf`;
      pdf.save(filename);
      showMessage('PDF generated and downloaded', 'success');
    }catch(err){ console.error(err); showMessage('PDF generation failed: '+(err && err.message ? err.message : err),'error'); }
  });

  releaseBtn.addEventListener('click', async ()=>{
    const empId = empInput.value.trim(); const period = periodEl.value; if(!empId || !period) { showMessage('Ensure employee and period selected','error'); return; }
    try{
      // send request to create payslip record
      const payload = new URLSearchParams(); payload.append('employee_id', empId); payload.append('period', period);
      const res = await fetch('/HRM/hrm_payroll/payslip_generation/payslip_generation_backend.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload.toString() });
      const json = await res.json(); if(json.status === 'success') { showMessage('Payslip released','success'); } else { showMessage('Release failed: '+(json.message||'unknown'),'error'); }
    }catch(err){ alert('Release error: '+err.message); }
  });
});
