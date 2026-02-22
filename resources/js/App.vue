<template>
  <div class="quotes-container">
    <header>
      <h1>Quotes Explorer</h1>
      <p>DummyJSON + Binary Search</p>
    </header>

    <section class="search-section">
      <h2>Search by ID</h2>
      <div class="search-box">
        <input 
          type="number" 
          v-model="searchId" 
          placeholder="Ex: 5" 
          @keyup.enter="searchQuote"
        />
        <button @click="searchQuote" :disabled="loading">
          {{ loading ? 'Searching...' : 'Search (Binary Search)' }}
        </button>
      </div>
      
      <p v-if="error" class="error-msg"> {{ error }}</p>

      <div v-if="singleQuote" class="quote-card highlight">
        <span class="badge">ID: {{ singleQuote.id }}</span>
        <p class="quote-text">"{{ singleQuote.quote }}"</p>
        <p class="author">- {{ singleQuote.author }}</p>
      </div>
    </section>

    <hr />

    <section class="list-section">
      <h2>All Cached Quotes</h2>
      
      <div v-if="quotes.length === 0" class="empty-state">
        No quotes in cache. Run the terminal command to import some!
      </div>

      <div v-else>
        <div class="grid">
          <div v-for="quote in paginatedQuotes" :key="quote.id" class="quote-card">
            <span class="badge">ID: {{ quote.id }}</span>
            <p class="quote-text">"{{ quote.quote }}"</p>
            <p class="author">- {{ quote.author }}</p>
          </div>
        </div>

        <div class="pagination">
          <button @click="prevPage" :disabled="currentPage === 1">⬅Previous</button>
          <span>Page {{ currentPage }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

// the data structure
interface Quote {
  id: number;
  quote: string;
  author: string;
}

// Reactive States
const quotes = ref<Quote[]>([]);
const singleQuote = ref<Quote | null>(null);
const searchId = ref<number | ''>('');
const error = ref<string>('');
const loading = ref<boolean>(false);

// Pagination Config
const currentPage = ref<number>(1);
const itemsPerPage = 6;

// API Call: Fetch all
const fetchQuotes = async () => {
  try {
    const response = await fetch('/api/quotes');
    quotes.value = await response.json();
  } catch (e) {
    console.error("Error loading quotes", e);
  }
};

// API Call: Binary Search by ID
const searchQuote = async () => {
  if (!searchId.value) return;
  
  loading.value = true;
  error.value = '';
  singleQuote.value = null;

  try {
    const response = await fetch(`/api/quotes/${searchId.value}`);
    if (!response.ok) {
      const errData = await response.json();
      throw new Error(errData.error || 'Rate limit exceeded or server error.');
    }
    
    singleQuote.value = await response.json();
    
    // Reload full list because if ID was new, backend cached it
    await fetchQuotes();
    
  } catch (e: any) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
};

const totalPages = computed(() => Math.ceil(quotes.value.length / itemsPerPage) || 1);
const paginatedQuotes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return quotes.value.slice(start, end);
});

const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };

onMounted(() => {
  fetchQuotes();
});
</script>

<style scoped>
.quotes-container {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  color: #333;
}
header {
  text-align: center;
  margin-bottom: 30px;
}
h1 { color: #2c3e50; }
.search-box {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}
input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
}
button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}
button:disabled { background-color: #a5d6a7; cursor: not-allowed; }
.error-msg { color: #d32f2f; font-weight: bold; }
.quote-card {
  background: #f9f9f9;
  border-left: 5px solid #4CAF50;
  padding: 15px;
  margin-bottom: 15px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.highlight { border-left-color: #ff9800; background: #fff8e1; }
.badge {
  background: #e0e0e0;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.8em;
  font-weight: bold;
}
.quote-text { font-size: 1.1em; font-style: italic; margin: 10px 0; }
.author { text-align: right; font-weight: bold; margin: 0; }
.grid { display: grid; gap: 15px; }
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding: 10px;
  background: #eee;
  border-radius: 6px;
}
hr { margin: 40px 0; border: 0; border-top: 1px solid #eee; }
.empty-state { text-align: center; padding: 40px; background: #ffebee; color: #c62828; border-radius: 8px; }
</style>