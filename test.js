function pivotBagTypeByDate(data) {
  const bagTypes = new Set();
  const grouped = new Map();

  // Langkah 1: Kumpulkan semua jenis bag_type selain "Bag"
  data.forEach(item => {
    if (item.bag_type !== "Bag") {
      bagTypes.add(item.bag_type);
    }
  });

  // Langkah 2: Kelompokkan data berdasarkan tanggal
  data.forEach(item => {
    if (item.bag_type === "Bag") return;

    if (!grouped.has(item.record_date)) {
      // Inisialisasi dengan semua bag_type = 0
      const entry = { record_date: item.record_date };
      bagTypes.forEach(type => entry[type] = 0);
      grouped.set(item.record_date, entry);
    }

    const entry = grouped.get(item.record_date);
    entry[item.bag_type] += item.total_quantity;
  });

  // Langkah 3: Pastikan semua tanggal punya semua bag_type
  for (const entry of grouped.values()) {
    bagTypes.forEach(type => {
      if (!(type in entry)) {
        entry[type] = 0;
      }
    });
  }

  return Array.from(grouped.values());
}



const rawData = [
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Bag",
    "total_quantity": 3
  },
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Granul",
    "total_quantity": 3
  },
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Subsidi",
    "total_quantity": 4
  },
  {
    "record_date": "2025-05-21",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-21",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Granul",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-25",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Bag",
    "total_quantity": 33
  },
  {
    "record_date": "2025-05-25",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-3",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Granul",
    "total_quantity": 33
  }
];

const result = pivotBagTypeByDate(rawData);
console.log(result);