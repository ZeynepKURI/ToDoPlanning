<template>
  <div>
    <h1>İş Yönetim Uygulaması</h1>
    <button @click="fetchAndSaveJobs">Verileri Çek ve Kaydet</button>
    <button @click="fetchSavedJobs">Kaydedilen Verileri Göster</button>
    <button @click="distributeJobs">İşleri Dağıt</button>

    <div v-if="loading">Yükleniyor...</div>

    <ul v-if="jobs.length">
      <li v-for="job in jobs" :key="job.id">
        {{ job.title }} - {{ job.time }} saat - Seviyem: {{ job.level }}
      </li>
    </ul>

    <div v-if="Object.keys(distribution).length">
      <h2>İş Dağıtımı</h2>
      <ul>
        <li v-for="(devJobs, devId) in distribution" :key="devId">
          Geliştirici {{ devId }}:
          <ul>
            <li v-for="job in devJobs" :key="job.id">
              {{ job.title }}
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
   
  },
  methods: {
    async fetchAndSaveJobs() {
      this.loading = true;
      try {
        const response = await axios.get('https://gist.githubusercontent.com/firatozpinar/8b6ac47e177f07bd99046f873154cef3/raw/b01e456f644370b1365363005c52631e182e66eb/gistfile1.txt');

console.log(response)
        await this.saveJobsToDatabase(jobs);
      } catch (error) {
        console.error("Veri kaydederken hata:", error.response ? error.response.data : error.message);
      } finally {
        this.loading = false;
      }
    },



    async fetchSavedJobs() {
  this.loading = true;
  try {
    const response = await axios.get('http://localhost:8000/api/jobs');
    this.jobs = response.data; // API'den dönen veriyi doğrudan alıyoruz
    console.log("Kaydedilen veriler:", this.jobs); // Verileri kontrol edin
  } catch (error) {
    console.error("Verileri çekerken hata:", error);
  } finally {
    this.loading = false;
  }
},


    async distributeJobs() {
      this.loading = true;
      try {
        const response = await axios.post('http://localhost:8000/api/jobs/distribute', { jobs: this.jobs });
        this.distribution = response.data.distribution;
        console.log("İşler başarıyla dağıtıldı:", this.distribution);
      } catch (error) {
        console.error("İşleri dağıtırken hata:", error.response ? error.response.data : error.message);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
