import React, { useState } from 'react';
import { Globe, ChevronDown, ChevronUp, MessageCircle, Layout, Zap, Search, Facebook, Send, Clock, ClipboardList } from 'lucide-react';

const App = () => {
  const [language, setLanguage] = useState('ur'); // Default to Urdu based on latest request
  const [openIndex, setOpenIndex] = useState(null);

  const contactLinks = {
    whatsapp: "https://wa.me/923323320369",
    facebook: "http://fb.com/moon060781",
    phpForm: "send_message.php"
  };

  const content = {
    en: {
      title: "Frequently Asked Questions",
      subtitle: "Professional and transparent web services for your business.",
      toggleBtn: "اردو میں دیکھیں",
      cta: "Still have questions?",
      contactBtn: "Chat on WhatsApp",
      fbBtn: "Message on Facebook",
      formBtn: "Send Online Message",
      faqs: [
        {
          q: "What is the 20k Website Package breakdown?",
          a: "The total cost is 20,000 PKR, which includes: Hosting (10k), Domain (5k), and Development (5k). This covers everything to get you started.",
          icon: <Layout className="w-5 h-5 text-blue-600" />
        },
        {
          q: "Is there a monthly maintenance fee?",
          a: "Yes, we charge 5,000 PKR per month for ongoing maintenance. This includes technical upkeep and monitoring your website messages/queries.",
          icon: <Zap className="w-5 h-5 text-yellow-500" />
        },
        {
          q: "How long does it take to go live?",
          a: "We are incredibly fast! Your website will be live within 2 to 10 hours once we have all the required information.",
          icon: <Clock className="w-5 h-5 text-green-500" />
        },
        {
          q: "What do you need from me to start?",
          a: "To begin, we need your brand name/logo, a brief description of your services, high-quality images (if any), and your contact details for the 'Contact Us' page.",
          icon: <ClipboardList className="w-5 h-5 text-purple-500" />
        },
        {
          q: "Will my website work on mobile phones?",
          a: "Yes, 100%. Every site we build is 'Mobile-First' to ensure it looks great on all smartphones and tablets.",
          icon: <Globe className="w-5 h-5 text-indigo-500" />
        }
      ]
    },
    ur: {
      title: "عام طور پر پوچھے گئے سوالات",
      subtitle: "آپ کے کاروبار کے لیے پیشہ ورانہ اور شفاف ویب سروسز۔",
      toggleBtn: "Switch to English",
      cta: "ابھی بھی کوئی سوال ہے؟",
      contactBtn: "واٹس ایپ پر چیٹ کریں",
      fbBtn: "فیس بک پر میسج کریں",
      formBtn: "آن لائن پیغام بھیجیں",
      faqs: [
        {
          q: "20 ہزار والے ویب سائٹ پیکیج کی تفصیل کیا ہے؟",
          a: "کل خرچہ 20,000 روپے ہے، جس کی تفصیل یہ ہے: ہوسٹنگ (10 ہزار)، ڈومین (5 ہزار)، اور ڈیولپمنٹ (5 ہزار)۔ یہ پیکیج ایک مکمل پروفیشنل ویب سائٹ کے لیے کافی ہے۔",
          icon: <Layout className="w-5 h-5 text-blue-600" />
        },
        {
          q: "کیا ماہانہ دیکھ بھال کے اخراجات ہیں؟",
          a: "جی ہاں، ویب سائٹ کی دیکھ بھال اور پیغامات (Messages) کو مانیٹر کرنے کے لیے ماہانہ 5,000 روپے فیس چارج کی جاتی ہے۔",
          icon: <Zap className="w-5 h-5 text-yellow-500" />
        },
        {
          q: "ویب سائٹ لائیو ہونے میں کتنا وقت لگتا ہے؟",
          a: "ہم بہت کم وقت میں کام مکمل کرتے ہیں۔ تمام تفصیلات ملنے کے بعد صرف 2 سے 10 گھنٹے کے اندر آپ کی ویب سائٹ لائیو کر دی جائے گی۔",
          icon: <Clock className="w-5 h-5 text-green-500" />
        },
        {
          q: "ویب سائٹ کے لیے ہمیں آپ کو کیا فراہم کرنا ہوگا؟",
          a: "ہمیں آپ کی ویب سائٹ کے لیے لوگو (Logo)، آپ کے کام کی تفصیل (Text)، کچھ تصاویر، اور وہ معلومات درکار ہوں گی جو آپ رابطہ پیج پر دکھانا چاہتے ہیں۔",
          icon: <ClipboardList className="w-5 h-5 text-purple-500" />
        },
        {
          q: "کیا ویب سائٹ گوگل پر نظر آئے گی؟",
          a: "ہم ہر پیکیج میں بنیادی SEO شامل کرتے ہیں تاکہ آپ کا نام اور کاروبار گوگل سرچ رزلٹس میں آنا شروع ہو سکے۔",
          icon: <Search className="w-5 h-5 text-red-500" />
        }
      ]
    }
  };

  const toggleLanguage = () => {
    setLanguage(language === 'en' ? 'ur' : 'en');
    setOpenIndex(null);
  };

  const isUrdu = language === 'ur';

  return (
    <div className={`min-h-screen bg-slate-50 font-sans ${isUrdu ? 'text-right' : 'text-left'}`} dir={isUrdu ? 'rtl' : 'ltr'}>
      {/* Header Section */}
      <nav className="bg-white border-b sticky top-0 z-50 shadow-sm">
        <div className="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md">N</div>
            <span className="font-bold text-xl text-slate-800 tracking-tight">noorgee.pk<span className="text-blue-600">/Web</span></span>
          </div>
          
          <button 
            onClick={toggleLanguage}
            className="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-xl font-medium hover:bg-blue-100 transition-all border border-blue-200"
          >
            <Globe size={18} />
            {content[language].toggleBtn}
          </button>
        </div>
      </nav>

      {/* Hero Section */}
      <div className="bg-white py-16 border-b">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h1 className={`text-3xl md:text-5xl font-black text-slate-900 mb-6 ${isUrdu ? 'font-urdu' : ''}`}>
            {content[language].title}
          </h1>
          <p className={`text-slate-600 text-lg md:text-xl max-w-2xl mx-auto ${isUrdu ? 'leading-relaxed' : ''}`}>
            {content[language].subtitle}
          </p>
        </div>
      </div>

      {/* FAQ Accordion Section */}
      <main className="max-w-3xl mx-auto px-4 py-12">
        <div className="space-y-4">
          {content[language].faqs.map((faq, index) => (
            <div 
              key={index} 
              className="bg-white border border-slate-200 rounded-3xl overflow-hidden transition-all hover:shadow-lg hover:border-blue-200"
            >
              <button
                onClick={() => setOpenIndex(openIndex === index ? null : index)}
                className="w-full px-6 py-5 flex items-center justify-between text-slate-800 hover:bg-slate-50 transition-colors"
              >
                <div className={`flex items-center gap-4 ${isUrdu ? 'flex-row-reverse text-right' : 'flex-row'}`}>
                  <div className="flex-shrink-0 p-2.5 bg-slate-100 rounded-2xl">
                    {faq.icon}
                  </div>
                  <span className={`text-lg font-bold text-slate-900 ${isUrdu ? 'leading-relaxed' : ''}`}>
                    {faq.q}
                  </span>
                </div>
                {openIndex === index ? (
                  <ChevronUp className="text-blue-600 flex-shrink-0 mx-2" />
                ) : (
                  <ChevronDown className="text-slate-400 flex-shrink-0 mx-2" />
                )}
              </button>
              
              <div className={`transition-all duration-300 ease-in-out ${openIndex === index ? 'max-h-96' : 'max-h-0'} overflow-hidden`}>
                <div className={`px-8 pb-7 pt-2 text-slate-600 border-t border-slate-50 ${isUrdu ? 'text-lg leading-loose' : 'text-base leading-relaxed'}`}>
                  {faq.a}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Contact Selection Section */}
        <div className="mt-20 bg-white border-2 border-blue-100 rounded-[2.5rem] p-10 shadow-xl relative overflow-hidden">
          <div className="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
          
          <div className="text-center mb-10 relative z-10">
            <h2 className={`text-3xl font-black text-slate-900 mb-3 ${isUrdu ? 'font-urdu' : ''}`}>
              {content[language].cta}
            </h2>
            <p className="text-slate-500 text-lg">ہم آپ کی مدد کے لیے حاضر ہیں</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            {/* WhatsApp */}
            <a 
              href={contactLinks.whatsapp}
              target="_blank"
              rel="noopener noreferrer"
              className="flex flex-col items-center p-8 bg-green-50 rounded-[2rem] border border-green-100 hover:bg-green-100 transition-all group shadow-sm hover:shadow-md"
            >
              <div className="w-14 h-14 bg-green-500 text-white rounded-2xl flex items-center justify-center mb-4 group-hover:rotate-12 transition-transform shadow-lg shadow-green-200">
                <MessageCircle size={28} />
              </div>
              <span className="font-bold text-green-700 text-center">
                {content[language].contactBtn}
              </span>
            </a>

            {/* Facebook */}
            <a 
              href={contactLinks.facebook}
              target="_blank"
              rel="noopener noreferrer"
              className="flex flex-col items-center p-8 bg-blue-50 rounded-[2rem] border border-blue-100 hover:bg-blue-100 transition-all group shadow-sm hover:shadow-md"
            >
              <div className="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-4 group-hover:rotate-12 transition-transform shadow-lg shadow-blue-200">
                <Facebook size={28} />
              </div>
              <span className="font-bold text-blue-700 text-center">
                {content[language].fbBtn}
              </span>
            </a>

            {/* PHP Form */}
            <a 
              href={contactLinks.phpForm}
              className="flex flex-col items-center p-8 bg-slate-50 rounded-[2rem] border border-slate-200 hover:bg-slate-100 transition-all group shadow-sm hover:shadow-md"
            >
              <div className="w-14 h-14 bg-slate-800 text-white rounded-2xl flex items-center justify-center mb-4 group-hover:rotate-12 transition-transform shadow-lg shadow-slate-200">
                <Send size={28} />
              </div>
              <span className="font-bold text-slate-700 text-center">
                {content[language].formBtn}
              </span>
            </a>
          </div>
        </div>
      </main>

      <footer className="py-12 text-center text-slate-400 text-sm border-t bg-white mt-12">
        <div className="max-w-4xl mx-auto px-4">
          <p className="mb-2 font-bold text-slate-600">Contact: +92 332 3320369</p>
          <p className="mb-4">Noorgee Web Studio - Specialized in Fast Delivery & Reliable Support</p>
          <div className="w-12 h-1 bg-blue-100 mx-auto mb-4 rounded-full"></div>
          <p>&copy; {new Date().getFullYear()} Noorgee.pk - Built for Pakistan</p>
        </div>
      </footer>

      {/* Custom Styles for Urdu Font */}
      <style dangerouslySetInnerHTML={{ __html: `
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap');
        .font-urdu { font-family: 'Noto Nastaliq Urdu', serif; }
        [dir='rtl'] .font-urdu { line-height: 3.5rem; }
        [dir='rtl'] .font-urdu span { line-height: normal; }
        @media (max-width: 768px) {
          [dir='rtl'] .font-urdu { line-height: 3rem; }
        }
      `}} />
    </div>
  );
};

export default App;
