document.addEventListener("DOMContentLoaded", function () {
    const fileButton = document.getElementById('fileButton');
    const hiddenTextarea = document.getElementById('hiddenTextarea');
    let editor;
    let currentImages = [];

    ClassicEditor
        .create(document.querySelector('#edit'), {
            ckfinder: {
                uploadUrl: 'req/upload_images.php',
                openerMethod: 'modal'
            },
            toolbar: [
                'ckfinder', 'imageUpload', '|', 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
            ],
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .then(newEditor => {
            editor = newEditor;

            const extractImages = (htmlContent) => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlContent;
                const imgTags = tempDiv.getElementsByTagName('img');
                const imgSrcs = [];
                for (let img of imgTags) {
                    imgSrcs.push(img.src);
                }
                return imgSrcs;
            };

            currentImages = extractImages(editor.getData());

            editor.model.document.on('change:data', () => {
                const newContent = editor.getData();
                const newImages = extractImages(newContent);

                // البحث عن الصور التي تم حذفها وتأكيد الحذف
                const deletedImages = currentImages.filter(img => !newImages.includes(img) && img.startsWith('http'));
                currentImages = newImages;

                deletedImages.forEach(imgUrl => {
                    if (confirm(`هل أنت متأكد أنك تريد حذف الصورة: ${imgUrl}؟`)) {
                        fetch('req/delete_images.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ url: imgUrl })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error deleting image:', data.error);
                            } else {
                                console.log('Image deleted successfully:', imgUrl);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    } else {
                        // إعادة الصورة للمحرر إذا تم إلغاء الحذف
                        currentImages.push(imgUrl);
                        editor.setData(newContent + `<img src="${imgUrl}" />`);
                    }
                });

                hiddenTextarea.value = newContent;
                hiddenTextarea.dispatchEvent(new Event('input'));
            });

            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return {
                    upload: () => loader.file
                        .then(file => new Promise((resolve, reject) => {
                            const data = new FormData();
                            data.append('upload', file);

                            fetch('req/upload_images.php', {
                                method: 'POST',
                                body: data
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    reject(data.error);
                                } else {
                                    resolve({
                                        default: data.url
                                    });
                                }
                            })
                            .catch(error => {
                                reject('حدث خطأ أثناء الاتصال بالخادم.');
                            });
                        }))
                };
            };
        })
        .catch(error => {
            console.error(error);
        });

    fileButton.addEventListener('click', function () {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.docx';
        fileInput.onchange = function () {
            const file = this.files[0];
            if (!file) {
                alert('Please select a file.');
                return;
            }

            const reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = function () {
                const arrayBuffer = reader.result;

                mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                    .then(result => {
                        const htmlContent = result.value;
                        const images = result.messages.filter(msg => msg.type === 'base64').map(img => img.value);

                        const imageUploadPromises = images.map(imageBase64 => {
                            return fetch('req/upload_images.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ image: imageBase64 })
                            }).then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    console.error('Error uploading image:', data.error);
                                }
                                return data.url;
                            });
                        });

                        Promise.all(imageUploadPromises)
                            .then(imageUrls => {
                                let updatedHtmlContent = htmlContent;
                                images.forEach((imageBase64, index) => {
                                    const base64Regex = new RegExp(imageBase64, 'g');
                                    updatedHtmlContent = updatedHtmlContent.replace(base64Regex, imageUrls[index]);
                                });

                                const currentContent = editor.getData();
                                const mergedContent = currentContent + updatedHtmlContent;

                                editor.setData(mergedContent);

                                hiddenTextarea.value = editor.getData();
                                hiddenTextarea.dispatchEvent(new Event('input'));
                            });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            };
        };

        document.body.appendChild(fileInput);
        fileInput.click();
        fileInput.remove();
    });
});
