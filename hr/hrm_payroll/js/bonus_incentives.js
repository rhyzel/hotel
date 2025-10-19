// Bonus Incentives JS
document.addEventListener('DOMContentLoaded', function() {
  const empIdInput = document.getElementById('b_employee_id');
  const fullName = document.getElementById('b_full_name');
  const basicSalary = document.getElementById('b_basic_salary');
  const perfBonus = document.getElementById('b_performance_bonus');
  const incentives = document.getElementById('b_incentives');
  const bonusType = document.getElementById('b_bonus_type');
  const remarks = document.getElementById('b_remarks');
  const computeBtn = document.getElementById('b_compute');
  const saveBtn = document.getElementById('b_save');
  const messageBox = document.getElementById('b_message');
  const searchToggle = document.getElementById('b_search_toggle');

  let hideTimer = null;
  function showMessage(text, type='success'){
    if(!messageBox) return;
    messageBox.innerText = text;
    messageBox.className = 'inline-message ' + (type === 'success' ? 'success' : 'error');
    messageBox.style.display = 'block';
    if(hideTimer) clearTimeout(hideTimer);
    hideTimer = setTimeout(()=>{ messageBox.style.display='none'; }, 4000);
  }

  // Fetch employee details (debounced + Enter)
  function debounce(fn, wait){ let t = null; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }

  async function fetchEmployee(){
    const id = empIdInput.value.trim();
    if(!id){ fullName.value=''; return; }
    try{
      const form = new FormData();
      form.append('action','fetch');
      form.append('employee_id', id);
      const res = await fetch('/HRM/hrm_payroll/bonus_incentives/bonus_incentives_backend.php', { method: 'POST', body: form });
      const json = await res.json();
      if(json.success){
        fullName.value = json.data.full_name || '';
        // populate basic salary if backend provided it
        if(json.data.base_salary !== undefined){
          basicSalary.value = `₱ ${parseFloat(json.data.base_salary).toFixed(2)}`;
        }
      } else {
        fullName.value = '';
        // show message but don't spam while typing
        showMessage(json.message || 'Employee not found', 'error');
      }
    }catch(err){
      showMessage('Network or server error while fetching employee', 'error');
    }
  }

  const debouncedFetch = debounce(fetchEmployee, 450);
  empIdInput.addEventListener('input', debouncedFetch);
  empIdInput.addEventListener('keydown', (ev)=>{
    if(ev.key === 'Enter'){
      ev.preventDefault();
      fetchEmployee();
    }
  });

  // Search toggle behavior: clicking toggles active state; when active, fetch immediately and keep fetching on changes
  let searchActive = false;
  function setSearchActive(val){
    searchActive = !!val;
    if(searchToggle){
      searchToggle.setAttribute('aria-pressed', searchActive ? 'true' : 'false');
      searchToggle.style.background = searchActive ? '#1e8f3e' : '#ffd700';
      // toggle dark-mode on the bonuses container for visual emphasis
      const container = document.querySelector('.bonuses-content');
      if(container){
        if(searchActive) container.classList.add('dark-mode'); else container.classList.remove('dark-mode');
      }
    }
  }
  if(searchToggle){
    searchToggle.addEventListener('click', (e)=>{
      e.preventDefault();
      setSearchActive(!searchActive);
      if(searchActive){ fetchEmployee(); }
    });
  }

  // If searchActive, fetch on input immediately (no debounce)
  empIdInput.addEventListener('input', (e)=>{
    if(searchActive){
      fetchEmployee();
    }
  });

  // Auto-fill performance bonus based on bonus type selection
  function applyBonusType(){
    if(!bonusType) return;
    const opt = bonusType.selectedOptions[0];
    const preset = opt ? parseFloat(opt.dataset.amount || 0) : 0;
    const holidayPct = opt ? parseFloat(opt.dataset.percentage || 0) : 0;
    const val = preset || 0;
    if(holidayPct > 0){
      // holiday option: compute as base_salary * (percentage/100)
      const raw = basicSalary && basicSalary.value ? basicSalary.value.replace(/[^0-9.]/g,'') : '';
      const base = raw ? parseFloat(raw) : 0;
      if(base > 0){
        const amount = base * (holidayPct/100);
        perfBonus.value = amount.toFixed(2);
        perfBonus.readOnly = true;
      } else {
        perfBonus.readOnly = false; // allow manual if no base salary available yet
      }
    } else if(opt && opt.value === '13th_month'){
      // attempt to use basic salary if present
      const raw = basicSalary && basicSalary.value ? basicSalary.value.replace(/[^0-9.]/g,'') : '';
      const base = raw ? parseFloat(raw) : 0;
      if(base > 0){
        perfBonus.value = base.toFixed(2);
        perfBonus.readOnly = true;
      } else {
        perfBonus.readOnly = false; // allow fill if no base
      }
    } else if(preset > 0){
      perfBonus.value = val.toFixed(2);
      perfBonus.readOnly = true;
    } else {
      // custom or manual types
      perfBonus.readOnly = false;
    }
  }

  if(bonusType){
    bonusType.addEventListener('change', applyBonusType);
    // run on load
    applyBonusType();
  }

  // Load holiday list from backend and populate select
  async function loadHolidays(){
    try{
      const form = new FormData();
      form.append('action','holidays');
      const res = await fetch('/HRM/hrm_payroll/bonus_incentives/bonus_incentives_backend.php', { method:'POST', body: form });
      const json = await res.json();
      if(json.success && Array.isArray(json.data)){
        // add holiday options after existing ones, and attach data-percentage
        json.data.forEach(h => {
          const opt = document.createElement('option');
          opt.value = `holiday_${h.id}`;
          opt.text = `${h.name} (${h.date}) - ${parseFloat(h.percentage).toFixed(2)}%`;
          opt.dataset.holidayId = h.id;
          opt.dataset.percentage = h.percentage;
          bonusType.appendChild(opt);
        });
        // if custom UI exists, refresh it
        if(window.__customBonusSelect) window.__customBonusSelect.refreshOptions();
      }
    }catch(err){
      // don't block if holidays fail
      console.warn('Failed to load holidays', err);
    }
  }
  loadHolidays();

  // --- Custom dropdown widget to reliably style options across browsers ---
  function createCustomSelect(selectEl){
    if(!selectEl) return null;
    // hide native select but keep it for form semantics
    selectEl.style.display = 'none';
    const wrapper = document.createElement('div');
    wrapper.className = 'custom-select';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'custom-select-trigger';
    trigger.textContent = selectEl.options[selectEl.selectedIndex]?.text || 'Select...';
    const optionsPane = document.createElement('div');
    optionsPane.className = 'custom-options';

    function buildOptions(){
      optionsPane.innerHTML = '';
      Array.from(selectEl.options).forEach(opt=>{
        const span = document.createElement('div');
        span.className = 'custom-option';
        span.tabIndex = 0;
        span.dataset.value = opt.value;
        // copy dataset attributes
        for(const k of opt.dataset ? Object.keys(opt.dataset) : []){ span.dataset[k] = opt.dataset[k]; }
        span.textContent = opt.text;
        span.addEventListener('click', ()=>{ selectValue(opt.value); closeOptions(); });
        span.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ selectValue(opt.value); closeOptions(); } });
        optionsPane.appendChild(span);
      });
    }

    function selectValue(val){
      selectEl.value = val;
      // update trigger label
      const sel = selectEl.options[selectEl.selectedIndex];
      trigger.textContent = sel ? sel.text : val;
      // fire change event so existing handlers run
      const ev = new Event('change', { bubbles:true });
      selectEl.dispatchEvent(ev);
    }

    function openOptions(){ wrapper.classList.add('open'); }
    function closeOptions(){ wrapper.classList.remove('open'); }

    trigger.addEventListener('click', ()=>{ if(wrapper.classList.contains('open')) closeOptions(); else openOptions(); });
    document.addEventListener('click', (e)=>{ if(!wrapper.contains(e.target)) closeOptions(); });

    wrapper.appendChild(trigger);
    wrapper.appendChild(optionsPane);
    selectEl.parentNode.insertBefore(wrapper, selectEl.nextSibling);

    const widget = {
      refreshOptions: buildOptions,
      open: openOptions,
      close: closeOptions,
      selectValue: selectValue,
      el: wrapper
    };
    buildOptions();
    return widget;
  }

  // create custom dropdown for bonusType and expose for holiday refresh
  if(bonusType){ window.__customBonusSelect = createCustomSelect(bonusType); }

  // Compute total (simple sum)
  computeBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    const p = parseFloat(perfBonus.value) || 0;
    const i = parseFloat(incentives.value) || 0;
    const total = p + i;
    showMessage(`Total bonus/incentive: ₱ ${total.toFixed(2)}`, 'success');
  });

  // Save record
  saveBtn.addEventListener('click', async (e)=>{
    e.preventDefault();
    const id = empIdInput.value.trim();
    if(!id){ showMessage('Provide Employee ID', 'error'); return; }
    const payload = new FormData();
    payload.append('action','save');
    payload.append('employee_id', id);
    payload.append('type', bonusType.value);
    payload.append('amount', (parseFloat(perfBonus.value)||0) + (parseFloat(incentives.value)||0));
    payload.append('description', remarks.value || '');

    try{
      const res = await fetch('/HRM/hrm_payroll/bonus_incentives/bonus_incentives_backend.php', { method: 'POST', body: payload });
      const json = await res.json();
      if(json.success){
        showMessage('Saved bonus/incentive successfully', 'success');
      } else {
        showMessage(json.message || 'Failed to save', 'error');
      }
    }catch(err){
      showMessage('Network or server error while saving', 'error');
    }
  });
});
