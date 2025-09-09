<!DOCTYPE  html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Equipment & Assets Management</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='%234F46E5' d='M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z'/></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
        height: 100%;
  font-family: 'Outfit', sans-serif;
  background: url('hotel_room.jpg') no-repeat center center fixed;
  background-size: cover;
    }
            .overlay {
  background: rgba(0, 0, 0, 0.65);
  background-size:cover;
  min-height: 100vh;}
  
          .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    .card {
              border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
      padding: 30px;
      margin-bottom: 20px;
    }
    .card h2 {
        color:white;
    }
    .card mb-8 {
        color:white;
    }
    
    label{
        color:white;
    }
    .asset-item {
      background: #F9FAFB;
      border: 1px solid #E5E7EB;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
    }
    .asset-item:hover {
      background: #F3F4F6;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    .available { background-color: #D1FAE5; color: #065F46; }
    .in-use { background-color: #DBEAFE; color: #1E3A8A; }
    .maintenance { background-color: #FEE2E2; color: #991B1B; }
    .condition-excellent { background-color: #D1FAE5; color: #065F46; }
    .condition-good { background-color: #DBEAFE; color: #1E3A8A; }
    .condition-fair { background-color: #FEF3C7; color: #92400E; }
    .condition-poor { background-color: #FEE2E2; color: #991B1B; }
    .tab-button {
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    .tab-button.active {
      background: #4F46E5;
      color: white;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }
    .tab-button:not(.active) {
      background: white;
      color: #374151;
      border-color: #E5E7EB;
    }
    .tab-button:not(.active):hover {
      background: #F9FAFB;
      border-color: #D1D5DB;
    }
    .modal {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(8px);
    }
    .icon {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }
   @media (max-width: 1024px) {
  .grid {
    flex-direction: column;
  }
}
            .footer {
  position: bottom;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 10px;
  background: #111827;
  color: #f9fafb;
  font-size: 10px;
  border-top: 1px solid #374151;

  display: flex;
  justify-content: center; /* centers horizontally */
  align-items: center;     /* centers vertically */
  text-align: center;
}
  </style>
</head>
      <div class="overlay">
    <div class="container">
  <div class="container mx-auto max-w-7xl">
   <div class="card mb-8">
    <h1 style="font-size: 35px; text-align: center; font-weight: bold; color: white;">
    <a href="maintenance.php" style="text-decoration: none; color: white;">
     EQUIPMENT ASSETS AND REGISTER
    </a>
  </h1>
  <p style="color: white; text-align: center; font-size: 14px;">
    Management system for equipment and inventory control
  </p>
</div>


    <div class="flex flex-wrap gap-4 mb-6">
      <button onclick="setActiveTab('all')" class="tab-button active" id="tab-all">All Assets</button>
      <button onclick="setActiveTab('housekeeping')" class="tab-button flex items-center gap-2" id="tab-housekeeping">
        <svg class="icon" viewBox="0 0 24 24"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M12 2v2m6.364.636l-1.414 1.414M21 12h-2m-.636 6.364l-1.414-1.414M12 21v-2m-6.364-.636l1.414-1.414M3 12h2m.636-6.364l1.414 1.414"/></svg>
        Housekeeping
      </button>
      <button onclick="setActiveTab('room')" class="tab-button flex items-center gap-2" id="tab-room">
        <svg class="icon" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
        Room Management
      </button>
      <button onclick="setActiveTab('inventory')" class="tab-button flex items-center gap-2" id="tab-inventory">
        <svg class="icon" viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"/></svg>
        Inventory
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1">
        <div class="card">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-3">
            <svg class="icon text-indigo-600" viewBox="0 0 24 24"><path d="M12 5v14m7-7H5"/></svg>
            Add New Asset
          </h2>
          
          <form id="assetForm" class="space-y-4">
            <div>
              <label class="block text-white font-medium mb-2">Asset Name</label>
              <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Type</label>
              <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="Cleaning Equipment">Cleaning Equipment</option>
                <option value="Furniture">Furniture</option>
                <option value="Lighting">Lighting</option>
                <option value="Electronics">Electronics</option>
                <option value="Bathroom Fixtures">Bathroom Fixtures</option>
                <option value="Kitchen Equipment">Kitchen Equipment</option>
                <option value="HVAC">HVAC</option>
                <option value="Safety Equipment">Safety Equipment</option>
                <option value="Linens">Linens</option>
                <option value="Other">Other</option>
              </select>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Category</label>
              <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="housekeeping">Housekeeping</option>
                <option value="room">Room Management</option>
                <option value="inventory">Inventory</option>
              </select>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Status</label>
              <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="available">Available</option>
                <option value="in-use">In Use</option>
                <option value="maintenance">Maintenance</option>
              </select>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Location</label>
              <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Room Number (Optional)</label>
              <input type="text" name="room" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Condition</label>
              <select name="condition" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="excellent">Excellent</option>
                <option value="good">Good</option>
                <option value="fair">Fair</option>
                <option value="poor">Poor</option>
              </select>
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Purchase Date</label>
              <input type="date" name="purchaseDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Assigned To (Optional)</label>
              <input type="text" name="assignedTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
              <label class="block text-white font-medium mb-2">Image URL (Optional)</label>
              <input type="url" name="image" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 font-semibold">
              <svg class="icon" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
              Register Asset
            </button>
          </form>
          
          <div id="formMessage" class="hidden mt-4 p-3 bg-green-100 text-green-800 rounded-lg">Asset added successfully!</div>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div class="card">
          <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3 flex-1">
              <svg class="icon text-indigo-600" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
              Asset Registry
            </h2>
            
            <div class="relative">
              <svg class="icon absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
              </svg>
              <input 
                type="text" 
                id="searchInput"
                placeholder="Search assets..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full sm:w-64"
              >
            </div>
          </div>
          
          <div id="assetList" class="space-y-4 max-h-96 overflow-y-auto">
            <!-- Assets will be populated here -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 modal hidden flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4" id="modalTitle">Edit Asset</h3>
        
        <form id="modalForm" class="space-y-4">
          <input type="hidden" id="modalAssetId">
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">Asset Name</label>
            <input type="text" id="modalName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">Status</label>
            <select id="modalStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
              <option value="available">Available</option>
              <option value="in-use">In Use</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">Condition</label>
            <select id="modalCondition" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
              <option value="excellent">Excellent</option>
              <option value="good">Good</option>
              <option value="fair">Fair</option>
              <option value="poor">Poor</option>
            </select>
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">Location</label>
            <input type="text" id="modalLocation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
          </div>
          
          <div class="flex gap-3 pt-4">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
              Update Asset
            </button>
            <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
  <script>
    let assets = [
      {
        id: 1,
        name: 'Vacuum Cleaner Pro',
        type: 'Cleaning Equipment',
        category: 'housekeeping',
        status: 'available',
        location: 'Housekeeping Storage',
        condition: 'good',
        purchaseDate: '2023-01-15',
        assignedTo: '',
        image: 'https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?w=400&h=300&fit=crop'
      },
      {
        id: 2,
        name: 'King Size Bed Frame',
        type: 'Furniture',
        category: 'room',
        status: 'in-use',
        location: 'Floor 3',
        room: '301',
        condition: 'excellent',
        purchaseDate: '2022-08-10',
        assignedTo: '',
        image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop'
      },
      {
        id: 3,
        name: 'Designer Table Lamp',
        type: 'Lighting',
        category: 'room',
        status: 'maintenance',
        location: 'Floor 2',
        room: '205',
        condition: 'fair',
        purchaseDate: '2023-03-20',
        assignedTo: 'Maintenance Team',
        image: 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=400&h=300&fit=crop'
      },
      {
        id: 4,
        name: 'Industrial Dishwasher',
        type: 'Kitchen Equipment',
        category: 'inventory',
        status: 'available',
        location: 'Kitchen Storage',
        condition: 'excellent',
        purchaseDate: '2023-06-01',
        assignedTo: '',
        image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop'
      }
    ];

    let activeTab = 'all';
    let searchTerm = '';

    function setActiveTab(tab) {
      activeTab = tab;
      
      // Update tab styles
      document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
      document.getElementById(`tab-${tab}`).classList.add('active');
      
      // Update category select in form
      if (tab !== 'all') {
        document.querySelector('[name="category"]').value = tab;
      }
      
      renderAssets();
    }

    function renderAssets() {
      const filtered = assets.filter(asset => {
        const matchesTab = activeTab === 'all' || asset.category === activeTab;
        const matchesSearch = !searchTerm || 
          asset.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
          asset.type.toLowerCase().includes(searchTerm.toLowerCase()) ||
          asset.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
          (asset.room && asset.room.toLowerCase().includes(searchTerm.toLowerCase()));
        return matchesTab && matchesSearch;
      });

      const assetList = document.getElementById('assetList');
      
      if (filtered.length === 0) {
        assetList.innerHTML = `
          <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none">
              <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z" stroke="currentColor" stroke-width="2"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No assets found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms or add a new asset.</p>
          </div>
        `;
        return;
      }

      assetList.innerHTML = filtered.map(asset => `
        <div class="asset-item">
          <div class="flex flex-col sm:flex-row gap-4">
            ${asset.image ? `
              <img src="${asset.image}" alt="${asset.name}" class="w-full sm:w-24 h-24 object-cover rounded-lg" 
                   onerror="this.style.display='none';">
            ` : ''}
            
            <div class="flex-1 min-w-0">
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-lg text-gray-900 truncate">${asset.name}</h3>
                <div class="flex gap-2 ml-4">
                  <button onclick="editAsset(${asset.id})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </button>
                  <button onclick="deleteAsset(${asset.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="icon" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </div>
              </div>
              
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                  <svg class="icon w-4 h-4" viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"/></svg>
                  <span>${asset.type} • ${asset.category.charAt(0).toUpperCase() + asset.category.slice(1)}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="icon w-4 h-4" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span>${asset.location}${asset.room ? ` - Room ${asset.room}` : ''}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="icon w-4 h-4" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  <span>Purchased: ${asset.purchaseDate || 'N/A'}</span>
                </div>
                ${asset.assignedTo ? `
                  <div class="flex items-center gap-2">
                    <svg class="icon w-4 h-4" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Assigned to: ${asset.assignedTo}</span>
                  </div>
                ` : ''}
              </div>
              
              <div class="flex gap-2 mt-3">
                <span class="status-badge ${asset.status}">${asset.status.replace('-', ' ')}</span>
                <span class="status-badge condition-${asset.condition}">${asset.condition}</span>
              </div>
            </div>
          </div>
        </div>
      `).join('');
    }

    function editAsset(id) {
      const asset = assets.find(a => a.id === id);
      if (!asset) return;

      document.getElementById('modalAssetId').value = asset.id;
      document.getElementById('modalName').value = asset.name;
      document.getElementById('modalStatus').value = asset.status;
      document.getElementById('modalCondition').value = asset.condition;
      document.getElementById('modalLocation').value = asset.location;
      
      document.getElementById('modal').classList.remove('hidden');
    }

    function deleteAsset(id) {
      if (confirm('Are you sure you want to delete this asset?')) {
        assets = assets.filter(a => a.id !== id);
        renderAssets();
      }
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
    }

    function showMessage() {
      const message = document.getElementById('formMessage');
      message.classList.remove('hidden');
      setTimeout(() => message.classList.add('hidden'), 3000);
    }

    // Event listeners
    document.getElementById('assetForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      
      const newAsset = {
        id: Date.now(),
        name: formData.get('name'),
        type: formData.get('type'),
        category: formData.get('category'),
        status: formData.get('status'),
        location: formData.get('location'),
        room: formData.get('room'),
        condition: formData.get('condition'),
        purchaseDate: formData.get('purchaseDate'),
        assignedTo: formData.get('assignedTo'),
        image: formData.get('image')
      };
      
      assets.push(newAsset);
      renderAssets();
      e.target.reset();
      showMessage();
    });

    document.getElementById('modalForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const id = parseInt(document.getElementById('modalAssetId').value);
      const asset = assets.find(a => a.id === id);
      
      if (asset) {
        asset.name = document.getElementById('modalName').value;
        asset.status = document.getElementById('modalStatus').value;
        asset.condition = document.getElementById('modalCondition').value;
        asset.location = document.getElementById('modalLocation').value;
        
        renderAssets();
        closeModal();
      }
    });

    document.getElementById('searchInput').addEventListener('input', function(e) {
      searchTerm = e.target.value;
      renderAssets();
    });

    document.getElementById('modal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    // Initial render
    renderAssets();
  </script>
  <footer class="footer">
  <p>© 2025 Hotel Maintenance and Engineering | All Rights Reserved</p>
</footer>
</body>
</html>
 