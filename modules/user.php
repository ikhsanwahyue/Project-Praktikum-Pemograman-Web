<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>
  <body>
    <div class="navbar">

    </div>

    <div class="hero">
        <h2 class="text-2xl font-semibold mb-6 text-left text-gray-700">Hubungi Kami</h2>
        <h3 class="text-1xl font-normal text-left text-gray-600">Kami siap membantu Anda! Jangan ragu untuk menghubungi kami dengan pertanyaan atau kebutuhan Anda.</h3>

        <div class="namalengkap">
            <form action="#" method="POST" class="space-y-4">
            <div>
                <label for="namaLengkap" class="block mb-1 font-medium text-gray-700">Nama Lengkap</label>
                <input id="namaLengkap" name="namaLengkap" type="text"
                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Masukkan nama lengkap Anda" required>
            </div>
            <button type="submit"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">KIRIM PESAN</button>
      </form>
        </div>
    </div>
  </body>
</html>